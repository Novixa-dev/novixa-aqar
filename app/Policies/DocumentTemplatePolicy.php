<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\DocumentTemplate;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentTemplatePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_document_template');
    }

    public function view(AuthUser $authUser, DocumentTemplate $documentTemplate): bool
    {
        return $authUser->can('view_document_template');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_document_template');
    }

    public function update(AuthUser $authUser, DocumentTemplate $documentTemplate): bool
    {
        return $authUser->can('update_document_template');
    }

    public function delete(AuthUser $authUser, DocumentTemplate $documentTemplate): bool
    {
        return $authUser->can('delete_document_template');
    }

    public function restore(AuthUser $authUser, DocumentTemplate $documentTemplate): bool
    {
        return $authUser->can('restore_document_template');
    }

    public function forceDelete(AuthUser $authUser, DocumentTemplate $documentTemplate): bool
    {
        return $authUser->can('force_delete_document_template');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_document_template');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_document_template');
    }

    public function replicate(AuthUser $authUser, DocumentTemplate $documentTemplate): bool
    {
        return $authUser->can('replicate_document_template');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_document_template');
    }

}