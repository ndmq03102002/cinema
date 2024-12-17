<?php

namespace App\Http\Controllers\Api\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Role;
use App\Models\Cinema;
use App\Models\Profile;
use App\Models\Evaluate;
use App\Models\Bill;
use App\Models\Ticket;
use App\Models\Promotion_User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class AccountController extends Controller
{
    public function index()
    {
        $manager = Auth::user();
        $cinemaId = $manager->cinema_id;

        if ($manager->role_id == 1) {
            // Eager load profile
            $accounts = Account::with('profile')->get();
            $roles = Role::all();
        } else {
            // Eager load profile
            $accounts = Account::where('cinema_id', $cinemaId)->where('role_id','!=','1')->with('profile')->get();
            $roles = Role::where('name', '!=', 'admin')->get();
        }

        $cinemas = Cinema::all();


        return view('admin.account.index', compact('accounts', 'roles', 'cinemas'));
    }

    
    public function create()
    {
        $config['method'] = 'create';
        if(Auth::user()->role_id == 1) {
            $roles = Role::pluck('name','id');
            $cinemas = Cinema::pluck('name','id');
        } else if(Auth::user()->role_id == 2) {
            $cinema_id = Auth::user()->cinema_id;
            $cinemas = Cinema::find($cinema_id);
            $roles = Role::where('name', '!=', 'admin')->pluck('name', 'id');
        }
       
        return view('admin.account.create', compact('config','roles','cinemas'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|unique:ci_account,email',
            'username' => 'required|unique:ci_account,username',
            'password' => 'required|min:6|max:55',
            'name' => 'required|string|max:255',
            'age' => 'required|integer',
            'phone_number' => 'required|string|max:15',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',  // avatar có thể là URL hoặc đường dẫn ảnh
        ], [
            'email.unique' => 'Email đã tồn tại',
            'email.email' => 'Email sai định dạng',
            'email.required' => 'Email là bắt buộc',
            'password.required' => 'Mật khẩu là trường bắt buộc',
            'password.min' => 'Mật khẩu có độ dài tối thiểu là 6 kí tự',
            'password.max' => 'Mật khẩu có độ dài tối đa là 55 kí tự',
            'username.unique' => 'User name đã tồn tại',
            'name.required' => 'Tên là bắt buộc.',
            'name.string' => 'Tên phải là một chuỗi văn bản.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'age.required' => 'Tuổi là bắt buộc.',
            'age.integer' => 'Tuổi phải là một số nguyên.',
            'phone_number.required' => 'Số điện thoại là bắt buộc.',
            'phone_number.string' => 'Số điện thoại phải là một chuỗi văn bản.',
            'phone_number.max' => 'Số điện thoại không được vượt quá 15 ký tự.',
            'avatar.image' => 'Ảnh đại diện phải là một tệp hình ảnh.',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng: jpeg, png, jpg, gif, svg.',
            'avatar.max' => 'Ảnh đại diện không được vượt quá 10MB.',
            
        ]);
        $dataAccount = [
            'email' => $request['email'],
            'username' => $request['username'],
            'status' => 1,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'cinema_id' => $request->cinema_id,
        ];
        $account = Account::create(array_merge($dataAccount))->id;
        // Xử lý upload avatar nếu có
        if ($request->hasFile('avatar')) {
            $avatarResult =cloudinary()->upload($request->file('avatar')->getRealPath(), [
                'folder' => 'avatar',
                'upload_preset' => 'avatar-upload',
            ]);
            $avatar = $avatarResult->getSecurePath();
        }
        $dataProfile = [
            'account_id' => $account,
            'name' => $request['name'],
            'age' => $request['age'],   
            'phone_number' => $request['phone_number'],
            'avatar' => $avatar,
        ];

        $profile = Profile::create(array_merge($dataProfile));
      
        
        return redirect()->route("account.index")->with('success', 'Đăng ký thành công!');
    }
    public function edit($id)
    {
        $config['method'] = 'update';
        $account = Account::with('profile')->find($id); // Eager load profile
    
        if (Auth::user()->role_id == 1) {
            $roles = Role::pluck('name', 'id');
            $cinemas = Cinema::pluck('name', 'id');
        } else if (Auth::user()->role_id == 2) {
            $cinema_id = Auth::user()->cinema_id;
            $cinemas = Cinema::find($cinema_id);
            $roles = Role::where('name', '!=', 'admin')->pluck('name', 'id');
        }
    
        return view('admin.account.edit', compact('config', 'roles', 'cinemas', 'account'));
    }
   
    public function update(Request $request, $id)
    {
        if ($request->ajax()) {
            $account = Account::find($id);
    
            // Cập nhật trạng thái
            if ($request->has('status')) {
                $account->status = $request->status;
            } 
            if ($request->has('role_id')) {
                $account->role_id = $request->role_id;
            } 
            if ($request->has('cinema_id')) {
                $account->cinema_id = $request->cinema_id;
            } 
            $account->save();
    
            return response()->json(['success' => 'Thông tin đã được cập nhật']);
        }else{
            $validatedData = $request->validate([
                'email' => 'required|email|unique:ci_account,email,'.$id,
                'username' => 'required|unique:ci_account,username,'.$id,
                'name' => 'required|string|max:255',
                'age' => 'required|integer',
                'phone_number' => 'required|string|max:15',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',  // avatar có thể là URL hoặc đường dẫn ảnh
            ], [
                'email.unique' => 'Email đã tồn tại',
                'email.email' => 'Email sai định dạng',
                'email.required' => 'Email là bắt buộc',
                'username.unique' => 'User name đã tồn tại',
                'name.required' => 'Tên là bắt buộc.',
                'name.string' => 'Tên phải là một chuỗi văn bản.',
                'name.max' => 'Tên không được vượt quá 255 ký tự.',
                'age.required' => 'Tuổi là bắt buộc.',
                'age.integer' => 'Tuổi phải là một số nguyên.',
                'phone_number.required' => 'Số điện thoại là bắt buộc.',
                'phone_number.string' => 'Số điện thoại phải là một chuỗi văn bản.',
                'phone_number.max' => 'Số điện thoại không được vượt quá 15 ký tự.',
                'avatar.image' => 'Ảnh đại diện phải là một tệp hình ảnh.',
                'avatar.mimes' => 'Ảnh đại diện phải có định dạng: jpeg, png, jpg, gif, svg.',
                'avatar.max' => 'Ảnh đại diện không được vượt quá 10MB.',
                
            ]);
            $account = Account::find($id);
            $account->role_id = $request->role_id;
            $account->username = $request->username;
            $account->email = $request->email;
            $account->status = $request->status;
            $account->save();
        
            $profile = Profile::where('account_id', $id)->first();
        
            // Kiểm tra xem profile có tồn tại hay không
            if ($profile) {
                $profile->name = $request->name;
                $profile->age = $request->age;
                $profile->phone_number = $request->phone_number;
        
                // Xử lý avatar (giữ nguyên code của bạn)
                $oldAvatar = $profile->avatar;
                if ($oldAvatar) {
                    $path = parse_url($oldAvatar, PHP_URL_PATH);
                    $parts = explode('/avatar/', $path);
                    $avatarPart = 'avatar/' . pathinfo($parts[1], PATHINFO_FILENAME);
                }
        
                if ($request->hasFile('avatar')) {
                    $avatarResult = cloudinary()->upload($request->file('avatar')->getRealPath(), [
                        'folder' => 'avatar',
                        'upload_preset' => 'avatar-upload',
                    ]);
                    $profile->avatar = $avatarResult->getSecurePath();
        
                    if ($oldAvatar) {
                        cloudinary()->destroy($avatarPart);
                    }
                }
        
                $profile->save();
            } else {
                // Xử lý trường hợp không tìm thấy profile
                // 1. Tạo mới profile:
                $profile = new Profile();
                $profile->account_id = $id;
                $profile->name = $request->name;
                $profile->age = $request->age;
                $profile->phone_number = $request->phone_number;
        
                if ($request->hasFile('avatar')) {
                    $avatarResult = cloudinary()->upload($request->file('avatar')->getRealPath(), [
                        'folder' => 'avatar',
                        'upload_preset' => 'avatar-upload',
                    ]);
                    $profile->avatar = $avatarResult->getSecurePath();
                }
                
                $profile->save();
        
                // 2. Hoặc thông báo lỗi:
                // return redirect()->back()->with('error', 'Không tìm thấy profile tương ứng với tài khoản này.');
        
                // 3. Hoặc làm cả hai: tạo mới và thông báo
                // return redirect()->back()->with('warning', 'Không tìm thấy profile. Đã tạo mới profile cho tài khoản này.');
            }
        
            return redirect()->route("account.index")->with('success', 'Cập nhật thành công!');
        }
    }

    
    public function destroy($id)
    {
        $promotion = Promotion_User::where('account_id', $id)->delete();
       
        $bill = Bill::where('account_id', $id)->get();
        if($bill){
            foreach($bill as $item){
                $ticket = Ticket::where('bill_id', $item->id)->delete();
                $item->delete();
            }
        }
        $evaluate = Evaluate::where('account_id', $id)->delete();
        Profile::where('account_id', $id)->delete();
        Account::find($id)->delete();
        return redirect()->back()->with('success', 'Xóa account thành công.');
    }

    public function getOnlyTransed(Request $request)
    {    
        $manager = Auth::user();
        $cinemaId = $manager->cinema_id;

        if ($manager->role_id == 1) {
            // Eager load profile với withTrashed()
            $accounts = Account::with(['profile' => function ($query) {
                $query->withTrashed();
            }])->onlyTrashed()->get();
            $roles = Role::all();
        } else {
            // Eager load profile với withTrashed()
            $accounts = Account::where('cinema_id', $cinemaId)
                            ->where('role_id', '!=', '1')
                            ->with(['profile' => function ($query) {
                                $query->withTrashed();
                            }])
                            ->onlyTrashed()
                            ->get();
            $roles = Role::where('name', '!=', 'admin')->get();
        }

        $cinemas = Cinema::all();

        return view('admin.account.soft_delete', compact('accounts', 'roles', 'cinemas'));
    }
    public function restore($id)
    {
        $account = Account::withTrashed()->find($id);

        if ($account) {
            // Restore account
            $account->restore();

            // Restore related profile
            $profile = Profile::withTrashed()->where('account_id', $id)->first();
            if ($profile) {
                $profile->restore();
            }

            // Restore related promotion_users
            $promotionUsers = Promotion_User::withTrashed()->where('account_id', $id)->get();
            if ($promotionUsers) {
                foreach ($promotionUsers as $promotionUser) {
                    $promotionUser->restore();
                }
            }

            // Restore related bills and tickets
            $bills = Bill::withTrashed()->where('account_id', $id)->get();
            if ($bills) {
                foreach ($bills as $bill) {
                    $bill->restore();
                    $tickets = Ticket::withTrashed()->where('bill_id', $bill->id)->get();
                    if ($tickets) {
                        foreach ($tickets as $ticket) {
                            $ticket->restore();
                        }
                    }
                }
            }

            // Restore related evaluates
            $evaluates = Evaluate::withTrashed()->where('account_id', $id)->get();
            if ($evaluates) {
                foreach ($evaluates as $evaluate) {
                    $evaluate->restore();
                }
            }

            return redirect()->back()->with('success', 'Khôi phục account và các dữ liệu liên quan thành công.');
        } else {
            return redirect()->back()->with('error', 'Không tìm thấy account.');
        }
    }

    public function forceDelete($id)
    {
        $promotion = Promotion_User::withTrashed()->where('account_id', $id)->forceDelete();
       
        $bill = Bill::where('account_id', $id)->get();
        if($bill){
            foreach($bill as $item){
                $ticket = Ticket::withTrashed()->where('bill_id', $item->id)->forceDelete();
                $item->withTrashed()->forceDelete();
            }
        }
        $evaluate = Evaluate::withTrashed()->where('account_id', $id)->forceDelete();
        Profile::withTrashed()->where('account_id', $id)->forceDelete();
        Account::withTrashed()->find($id)->forceDelete();
        return redirect()->back()->with('success', 'Xóa account vĩnh viễn thành công.');
    }
  
}
