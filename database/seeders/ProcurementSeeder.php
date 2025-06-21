<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\Supplier;
use App\Models\Material;
use Carbon\Carbon;

class ProcurementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contracts = Contract::with('project')
            ->where('status', 'active')
            ->whereNotNull('project_id')
            ->whereHas('project')
            ->get();
        $employees = Employee::all();
        $suppliers = Supplier::all();
        $materials = Material::all();

        if ($contracts->isEmpty() || $employees->isEmpty() || $suppliers->isEmpty() || $materials->isEmpty()) {
            $this->command->info('Missing data for contracts, employees, suppliers, or materials. Skipping ProcurementSeeder.');
            return;
        }

        foreach ($contracts as $contract) {
            // 1. Create a Purchase Request
            $pr = PurchaseRequest::create([
                'request_number' => 'PR-' . time() . '-' . $contract->id,
                'contract_id' => $contract->id,
                'requested_by' => $employees->random()->user_id,
                'status' => 'approved',
                'notes' => 'Purchase request for project: ' . $contract->project->name,
            ]);

            $prTotal = 0;
            $orderItemsData = [];

            // Add 3 to 8 items to the PR
            for ($i = 0; $i < rand(3, 8); $i++) {
                $material = $materials->random();
                $quantity = rand(5, 50);
                $price = $material->srp_price;
                $total = $quantity * $price;
                $prTotal += $total;

                PurchaseRequestItem::create([
                    'purchase_request_id' => $pr->id,
                    'material_id' => $material->id,
                    'description' => $material->description ?? $material->name,
                    'quantity' => $quantity,
                    'unit' => $material->unit,
                    'estimated_unit_price' => $price,
                    'total_amount' => $total,
                ]);

                $orderItemsData[] = [
                    'material_id' => $material->id,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'total_price' => $total,
                ];
            }
            $pr->total_amount = $prTotal;
            $pr->save();

            // 2. Create a Purchase Order from the PR
            $supplier = $suppliers->random();
            $po = PurchaseOrder::create([
                'po_number' => 'PO-' . time() . '-' . $pr->id,
                'purchase_request_id' => $pr->id,
                'contract_id' => $contract->id,
                'supplier_id' => $supplier->id,
                'total_amount' => $prTotal,
                'status' => 'approved',
                'delivery_date' => Carbon::now()->addDays(rand(7, 30)),
                'payment_terms' => 'Net 30',
                'shipping_terms' => 'FOB Destination',
            ]);

            foreach ($orderItemsData as $itemData) {
                PurchaseOrderItem::create(array_merge($itemData, ['purchase_order_id' => $po->id]));
            }

            // 3. Create a Delivery for the PO
            $delivery = Delivery::create([
                'delivery_number' => 'DEL-' . time() . '-' . $po->id,
                'purchase_order_id' => $po->id,
                'delivery_date' => $po->delivery_date,
                'received_by' => $employees->random()->user_id,
                'status' => ['received', 'pending'][rand(0, 1)],
            ]);

            foreach ($po->items as $poItem) {
                DeliveryItem::create([
                    'delivery_id' => $delivery->id,
                    'material_id' => $poItem->material_id,
                    'quantity' => $poItem->quantity,
                ]);
            }
        }

        foreach ($materials as $material) {
            $basePrice = $material->srp_price ?? rand(100, 1000);

            // Create 5-10 historical purchase orders for each material over the last 6 months
            for ($i = 0; $i < rand(5, 10); $i++) {
                $date = Carbon::now()->subMonths(rand(0, 6))->subDays(rand(0, 30));
                $supplier = $suppliers->random();
                $prTotal = 0;

                // Create a dummy Purchase Request
                $pr = PurchaseRequest::create([
                    'request_number' => 'PR-HIST-' . time() . '-' . $material->id . '-' . $i,
                    'requested_by' => $employees->random()->user_id,
                    'status' => 'approved',
                    'notes' => 'Historical purchase request for ' . $material->name,
                ]);

                // Create a Purchase Order
                $po = PurchaseOrder::create([
                    'po_number' => 'PO-HIST-' . time() . '-' . $pr->id,
                    'purchase_request_id' => $pr->id,
                    'supplier_id' => $supplier->id,
                    'total_amount' => 0, // Will be updated
                    'status' => 'completed',
                    'delivery_date' => $date->copy()->addDays(14),
                    'payment_terms' => 'Net 30',
                    'shipping_terms' => 'FOB Destination',
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                // Fluctuate price slightly (+/- 15%)
                $price = $basePrice * (1 + (rand(-15, 15) / 100));
                $quantity = rand(10, 100);
                $total = $quantity * $price;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'material_id' => $material->id,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'total_price' => $total,
                ]);

                $po->total_amount = $total;
                $po->save();
            }
        }

        $this->command->info('Historical procurement data seeded successfully!');
    }
} 