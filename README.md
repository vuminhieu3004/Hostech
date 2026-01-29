<h1>Cách dùng RBAC </h1>
(VÍ DỤ KHI THÊM 1 TÍNH NĂNG : packing_lots) sau khi tạo migraton và models ta làm theo các bước:

<h2>B1: truy cập file config/rbac.php tại mục modules để định nghĩa các chức năng</h2>
<h3>Ví dụ :</h3>
$modules['parking_lots'] = ['view','create','update','delete']; //ĐÂY CHỈ LÀ CRUD THUẦN
ĐỐI VỚI CÁC CHỨC NĂNG NGHIỆP VỤ NHƯ invoices THÌ KHÔNG PHẢI CRUD THUẦN PHẢI ĐỂ TRONG CUSTOM
Ví dụ: 
$modules['invoices'] = $CRUD;
$custom[] = 'invoices.issue';
$custom[] = 'invoices.collect';
$custom[] = 'invoices.void';

<h2>B2: Khai báo scope</h2>
<h3>Ví dụ :</h3> 'parking_lots.*' => 'property',
*Chú ý ở đây có 2 options: nếu chức năng thuộc nhà/khu thì dùng "property"
còn nếu chức năng thuộc toàn hệ thống thì dùng "org".

<h2>B3: gán role cho phép ai được làm quyền gì</h2>
<h3>Ví dụ :</h3>
'Owner' => ['*'], (full quyền)

'Manager' => [
// ...
'parking_lots.*',
],

'Staff' => [
// ...
'parking_lots.view',
'parking_lots.create',
],

'Tenant' => [
// ...
'parking_lots.view',
],

<h2>B4: đồng bộ xuống Database</h2>
php artisan config:clear
php artisan rbac:sync

<h2>B5:Chú ý trong Controller khi truy vấn lấy hoặc thêm dữ liệu tuyệt đối KHÔNG ĐƯỢC QUÊN check trùng "org_id"</h2>
và "user_id" nếu không check hệ thống sẽ chặn
<h3>Ví dụ :</h3>
public function index(Request $request, string $property)
{
$user = $request->user();

        $data = ParkingLot::query()
            ->where('org_id', $user->org_id)          // CHECK Ở ĐÂY
            ->where('property_id', $property)          // CHECK Ở ĐÂY
            ->orderBy('name')
            ->paginate(20);

        return response()->json($data);
    }

public function store(Request $request, string $property)
{
$user = $request->user();

        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'capacity' => ['nullable','integer','min:0'],
            'note' => ['nullable','string'],
        ]);

        $lot = ParkingLot::create([
            'org_id' => $user->org_id,                           // CHECK Ở ĐÂY
            'property_id' => $property,                          // CHECK Ở ĐÂY
            'name' => $validated['name'],
            'capacity' => $validated['capacity'] ?? 0,
            'note' => $validated['note'] ?? null,
        ]);

        return response()->json($lot, 201);
    }

    // PUT /properties/{property}/parking-lots/{parkingLot}
    public function update(Request $request, string $property, ParkingLot $parkingLot)
    {
        $user = $request->user();

        // chặn sửa chéo org/property  // CHECK Ở ĐÂY
        if ($parkingLot->org_id !== $user->org_id || $parkingLot->property_id !== $property) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'name' => ['sometimes','string','max:255'],
            'capacity' => ['sometimes','integer','min:0'],
            'note' => ['nullable','string'],
        ]);

        $parkingLot->fill($validated)->save();

        return response()->json($parkingLot);
    }

<h2>B6: Khai báo router dùng middleware</h2>
<h3>Ví dụ :</h3>
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/properties/{property}/parking-lots', [ParkingLotController::class, 'index'])
        ->middleware('rbac:parking_lots.view,property');

    Route::post('/properties/{property}/parking-lots', [ParkingLotController::class, 'store'])
        ->middleware('rbac:parking_lots.create,property');

    Route::put('/properties/{property}/parking-lots/{parkingLot}', [ParkingLotController::class, 'update'])
        ->middleware('rbac:parking_lots.update,property');

    Route::delete('/properties/{property}/parking-lots/{parkingLot}', [ParkingLotController::class, 'destroy'])
        ->middleware('rbac:parking_lots.delete,property');

});
Lưu ý: khi trỏ đến middleware truyền định dạng chức năng của router đó và phạm vi hoạt động
("VD:'rbac:parking_lots.view,property','rbac:parking_lots.create,property',...")
