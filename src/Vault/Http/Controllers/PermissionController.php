<?php namespace Rappasoft\Vault\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Rappasoft\Vault\Repositories\Role\RoleRepositoryContract;
use Rappasoft\Vault\Repositories\Permission\PermissionRepositoryContract;
use Rappasoft\Vault\Exceptions\EntityNotValidException;
use Illuminate\Routing\Controller;

class PermissionController extends Controller {

	protected $roles;
	protected $permissions;

	public function __construct(RoleRepositoryContract $roles, PermissionRepositoryContract $permissions) {
		$this->roles = $roles;
		$this->permissions = $permissions;
	}

	/*
	 * Show the list of users
	 */
	public function index() {
		return view('vault::roles.permissions.index')
			->withPermissions($this->permissions->getPermissionsPaginated(50));
	}

	/*
	 * Create a user
	 */
	public function create() {
		return view('vault::roles.permissions.create')
			->withRoles($this->roles->getAllRoles());
	}

	/*
	 * Save a new user
	 */
	public function store() {
		try {
			$this->permissions->create(Input::except('permission_roles'), Input::only('permission_roles'));
		} catch (EntityNotValidException $e) {
			return Redirect::back()->with('input', Input::all())->withFlashDanger($e->validationErrors());
		} catch (Exception $e) {
			return Redirect::back()->with('input', Input::all())->withFlashDanger($e->getMessage());
		}

		return Redirect::route('access.roles.permissions.index')->withFlashSuccess("Permission successfully created.");
	}

	/*
	 * Edit the specified user
	 */
	public function edit($id) {
		try {
			$permission = $this->permissions->findOrThrowException($id, true);
			return view('vault::roles.permissions.edit')
				->withPermission($permission)
				->withPermissionRoles($permission->roles->lists('id')->all())
				->withRoles($this->roles->getAllRoles());
		} catch (Exception $e) {
			return Redirect::route('access.roles.permissions.index')->withFlashDanger($e->getMessage());
		}
	}

	/*
	 * Update the specified user
	 */
	public function update($id) {
		try {
			$this->permissions->update($id, Input::except('permission_roles'), Input::only('permission_roles'));
		} catch (EntityNotValidException $e) {
			return Redirect::back()->with('input', Input::all())->withFlashDanger($e->validationErrors());
		} catch (Exception $e) {
			return Redirect::back()->with('input', Input::all())->withFlashDanger($e->getMessage());
		}

		return Redirect::route('access.roles.permissions.index')->withFlashSuccess("Permission successfully updated.");
	}

	/*
	 * Delete the specified user
	 */
	public function destroy($id) {
		try {
			$this->permissions->destroy($id);
		} catch (Exception $e) {
			return Redirect::back()->withInput()->withFlashDanger($e->getMessage());
		}

		return Redirect::route('access.roles.permissions.index')->withFlashSuccess("Permission successfully deleted.");
	}

}