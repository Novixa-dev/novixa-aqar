<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ChatConversation;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatConversationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_chat_conversation');
    }

    public function view(AuthUser $authUser, ChatConversation $chatConversation): bool
    {
        return $authUser->can('view_chat_conversation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_chat_conversation');
    }

    public function update(AuthUser $authUser, ChatConversation $chatConversation): bool
    {
        return $authUser->can('update_chat_conversation');
    }

    public function delete(AuthUser $authUser, ChatConversation $chatConversation): bool
    {
        return $authUser->can('delete_chat_conversation');
    }

    public function restore(AuthUser $authUser, ChatConversation $chatConversation): bool
    {
        return $authUser->can('restore_chat_conversation');
    }

    public function forceDelete(AuthUser $authUser, ChatConversation $chatConversation): bool
    {
        return $authUser->can('force_delete_chat_conversation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_chat_conversation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_chat_conversation');
    }

    public function replicate(AuthUser $authUser, ChatConversation $chatConversation): bool
    {
        return $authUser->can('replicate_chat_conversation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_chat_conversation');
    }

}