@extends('layouts.chat')

@section('content')
<div class="container-fluid h-100" style="height:100vh;">
    <div class="row h-100" style="height:100vh;">
        <!-- Left Sidebar -->
        <div class="col-3 d-flex flex-column p-0 border-end bg-white" style="height:100vh;min-width:300px;max-width:350px;">
            <div class="d-flex align-items-center justify-content-between px-3 py-3 border-bottom">
                <h4 class="mb-0">Chats</h4>
                <div>
                    <button class="btn btn-light btn-sm me-2"><i class="fas fa-ellipsis-h"></i></button>
                    <button class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></button>
                </div>
            </div>
            <div class="px-3 py-2 border-bottom">
                <input type="text" class="form-control" placeholder="Search Messenger...">
            </div>
            <div class="d-flex px-3 py-2 border-bottom gap-3">
                <button class="btn btn-link p-0 text-primary fw-bold">All</button>
                <button class="btn btn-link p-0 text-secondary">Unread</button>
                <button class="btn btn-link p-0 text-secondary">Groups</button>
                <button class="btn btn-link p-0 text-secondary">Communities</button>
            </div>
            <div class="flex-grow-1 overflow-auto" style="background:#f8f9fa;">
                <!-- Example chat list -->
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center bg-primary text-white">
                        <img src="https://randomuser.me/api/portraits/men/1.jpg" class="rounded-circle me-3" width="48" height="48">
                        <div class="flex-grow-1">
                            <div class="fw-bold">modus operandi</div>
                            <small>Patricia Anne: para mas accessible · 1m</small>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                        <img src="https://randomuser.me/api/portraits/men/2.jpg" class="rounded-circle me-3" width="48" height="48">
                        <div class="flex-grow-1">
                            <div class="fw-bold">adse mhie 👀</div>
                            <small>You: ok po · 3m</small>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                        <img src="https://randomuser.me/api/portraits/men/3.jpg" class="rounded-circle me-3" width="48" height="48">
                        <div class="flex-grow-1">
                            <div class="fw-bold">Anti-Normies</div>
                            <small>BASTA BHI sent a photo. · 1h</small>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                        <img src="https://randomuser.me/api/portraits/women/4.jpg" class="rounded-circle me-3" width="48" height="48">
                        <div class="flex-grow-1">
                            <div class="fw-bold">Shella Mallari</div>
                            <small>thanksss ya · 1h</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <!-- Center Chat Area -->
        <div class="col-6 d-flex flex-column p-0" style="height:100vh;min-width:400px;">
            <!-- Chat Header -->
            <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom bg-white">
                <div class="d-flex align-items-center">
                    <img src="https://randomuser.me/api/portraits/men/1.jpg" class="rounded-circle me-3" width="48" height="48">
                    <div>
                        <div class="fw-bold">modus operandi</div>
                        <small>Patricia Anne Dela Cruz <span class="fw-bold">Attachment</span></small>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <button class="btn btn-link text-primary"><i class="fas fa-phone fa-lg"></i></button>
                    <button class="btn btn-link text-primary"><i class="fas fa-video fa-lg"></i></button>
                    <button class="btn btn-link text-primary"><i class="fas fa-info-circle fa-lg"></i></button>
                </div>
            </div>
            <!-- Messages -->
            <div class="flex-grow-1 px-4 py-3 overflow-auto" style="background:#f4f6fb;">
                <!-- Example messages -->
                <div class="d-flex flex-column gap-3">
                    <div class="align-self-end">
                        <div class="bg-primary text-white rounded-pill px-4 py-2 mb-1" style="max-width:60%;">Sige sige</div>
                        <div class="bg-primary text-white rounded-pill px-4 py-2 mb-1" style="max-width:60%;">So yung button mag rereddirect sa buong page?</div>
                        <div class="bg-primary text-white rounded-pill px-4 py-2" style="max-width:60%;">ay sige sige wag na ganto?</div>
                    </div>
                    <div class="align-self-start">
                        <div class="bg-light text-dark rounded-pill px-4 py-2 mb-1" style="max-width:60%;">YUS</div>
                        <div class="bg-light text-dark rounded-pill px-4 py-2 mb-1" style="max-width:60%;">pwede naman both</div>
                        <div class="bg-light text-dark rounded-pill px-4 py-2" style="max-width:60%;">para mas accessible</div>
                    </div>
                </div>
            </div>
            <!-- Message Input -->
            <div class="border-top bg-white px-4 py-3">
                <form class="d-flex align-items-center gap-2">
                    <button class="btn btn-link text-primary" type="button"><i class="fas fa-plus fa-lg"></i></button>
                    <button class="btn btn-link text-primary" type="button"><i class="fas fa-image fa-lg"></i></button>
                    <button class="btn btn-link text-primary" type="button"><i class="fas fa-sticky-note fa-lg"></i></button>
                    <button class="btn btn-link text-primary" type="button"><i class="fas fa-gift fa-lg"></i></button>
                    <input type="text" class="form-control rounded-pill" placeholder="Aa">
                    <button class="btn btn-link text-primary" type="button"><i class="far fa-smile fa-lg"></i></button>
                    <button class="btn btn-link text-primary" type="button"><i class="fas fa-thumbs-up fa-lg"></i></button>
                </form>
            </div>
        </div>
        <!-- Right Sidebar -->
        <div class="col-3 d-flex flex-column p-0 border-start bg-white" style="height:100vh;min-width:300px;max-width:350px;">
            <div class="d-flex flex-column align-items-center py-4 border-bottom">
                <img src="https://randomuser.me/api/portraits/men/1.jpg" class="rounded-circle mb-2" width="72" height="72">
                <div class="fw-bold">modus operandi</div>
            </div>
            <div class="p-4">
                <div class="mb-4">
                    <button class="btn btn-light w-100 mb-2"><i class="fas fa-bell me-2"></i>Mute</button>
                    <button class="btn btn-light w-100"><i class="fas fa-search me-2"></i>Search</button>
                </div>
                <div class="mb-4">
                    <div class="fw-bold mb-2">Chat info</div>
                    <div class="mb-2">Customize chat</div>
                    <div class="mb-2">Chat members</div>
                </div>
                <div>
                    <div class="fw-bold mb-2">Media, files and links</div>
                    <div class="mb-2"><i class="fas fa-photo-video me-2"></i>Media</div>
                    <div><i class="fas fa-file-alt me-2"></i>Files</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 