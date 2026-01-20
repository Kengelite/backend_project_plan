<?


namespace App\Services\Admin\Manage;

use App\Trait\Utils;
use App\Models\fileDataUpload;

class FileDataUploadService
{

    use Utils;
    public function getAll($perPage)
    {
        $data = fileDataUpload::orderByDesc('data_id')->paginate($perPage)->withQueryString();;
        return $data;
    }

    public function getAllByidfile($id, $perPage)
    {
        $data = fileDataUpload::with('activity')->where('id_file', $id)->orderByDesc('data_id')->paginate($perPage)->withQueryString();;
        return $data;
    }



}
