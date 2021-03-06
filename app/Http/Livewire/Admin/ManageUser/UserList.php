<?php

namespace App\Http\Livewire\Admin\ManageUser;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\User as UserModel;


class UserList extends Component
{
    use WithFileUploads;
    use WithPagination;
    use AuthorizesRequests;

    public $name;
    public $email;
    public $access_level;
    public $read_permission;
    public $write_permission;
    public $search;

    protected $queryString = [
        'search' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'name' => 'required|min:6',
        'email' => 'required|email',
        'access_level' => 'required|integer|min:0|max:255',
        'read_permission' => 'required|integer|min:0|max:1',
        'write_permission' => 'required|integer|min:0|max:1',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetErrorPopulate($user)
    {
        $this->resetErrorBag();
        $this->emit('reseted-populated', $user);
    }

    public function updateUser($user)
    {
        $this->authorize('manage-user');
        $id = $user['id'];
        foreach($user as $key => $value) {
            if (isset($this->rules[$key])) {
                $this->$key = $value;
            }
        }
        $this->validate();
        $user = UserModel::find($id);
        $whitelist = [...array_keys($this->rules)];
        foreach($whitelist as $key) {
            $user->$key = $this->$key;
        }
        $user->save();
        $this->emit('updated');
    }

    public function deleteUser($user) {
        $this->authorize('manage-user');
        $user = UserModel::find($user['id']);
        $user->delete();
        $this->emit('deleted');
    }

    public function render()
    {
        $q = $this->search;
        $userList = [];
        if ($q) {
          $userList = UserModel::where('id', '!=', Auth::user()->id)
             ->where(function($query) use ($q) {
                $query->where('email', 'like', '%' . $q . '%');
                $query->orWhere('name', 'like', '%' . $q . '%');
             })
            ->orderBy('access_level', 'asc')->paginate(15);
        } else {
            $userList = UserModel::where('id', '!=', Auth::user()->id)->orderBy('access_level', 'asc')->paginate(15);
        }
        return view('livewire.admin.manage-user.user-list', [
            'userList' => $userList,
        ]);
    }
}
