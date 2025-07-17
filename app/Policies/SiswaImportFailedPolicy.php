<?php

namespace App\Policies;

use App\Models\SiswaImportFailed;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SiswaImportFailedPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_siswa_import_failed');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SiswaImportFailed $siswaImportFailed): bool
    {
        return $user->can('view_siswa_import_failed');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_siswa_import_failed');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SiswaImportFailed $siswaImportFailed): bool
    {
        return $user->can('update_siswa_import_failed');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SiswaImportFailed $siswaImportFailed): bool
    {
        return $user->can('delete_siswa_import_failed');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SiswaImportFailed $siswaImportFailed): bool
    {
        return $user->can('restore_siswa_import_failed');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SiswaImportFailed $siswaImportFailed): bool
    {
        return $user->can('force_delete_siswa_import_failed');
    }
}
