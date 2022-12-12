<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageTemplates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function input(Request $request)
    {
        $this->validate($request, [
            'sender' => 'required|min:9',
            'content' => 'required|string',
            'sent_at' => 'nullable|date_format:Y-m-d H:i:s',
            'is_group' => 'nullable|numeric',
            'is_base64' => 'nullable|numeric',
        ]);

        $data = $request->all();

        
        if (isset($data['is_base64'])){
            $data['content'] = base64_decode($data['content']);
        }

        // ceck sender
        if (substr($data['sender'],0,2)=='08'){
            $data['sender'] = '628'.substr($data['sender'],2);
        }else if (substr($data['sender'],0,3)=='628'){
            $data['sender'] = $data['sender'];
        }else if (strpos($data['sender'], '@g.us') !== false){
            $data['sender'] = $data['sender'];
        }else{
            $result = [
                'status' => 'error',
                'data' => 'Data Sender harus berawal 628*** atau 08***.',
            ];
            return response()->json($result);
        }

        

        // cek type pesan
        if (isset($data['type'])){
            if (($data['type']=='image' || $data['type']=='document') && !isset($data['media'])){
                $result = [
                    'status' => 'error',
                    'data' => 'Data media wajib diisi dan harus berisi url yang valid',
                ];
                return response()->json($result);
            }

            if ($data['type']=='image' && !@getimagesize($data['media'])){
                $result = [
                    'status' => 'error',
                    'data' => 'Data media harus berisi url image yang valid',
                ];
                return response()->json($result);
            }
            
            
            if ($data['type']=='document'){
                $headerFile = get_headers($data['media'],true);

                if (strpos($headerFile[0], '200') === false) {
                    $result = [
                        'status' => 'error',
                        'data' => 'Data content harus berisi url document yang valid',
                    ];
                    return response()->json($result);
                }


                $filename= isset($data['options']['filename']) ? $data['options']['filename'] : basename($data['media']);
                $mimetype = isset($data['options']['mimetype']) ? $data['options']['mimetype'] : $headerFile['Content-Type'];
                $data['options'] = json_encode(['filename'=>$filename, 'mimetype'=>$mimetype],JSON_PRETTY_PRINT);
            }
        }

        $data['sent_at'] = (isset($data['sent_at']) ? $data['sent_at'] :  date("Y-m-d H:i:s"));
        $message = Message::create($data);



        $result = [
            'success' => $message,
        ];

        log_activity('input send message', json_encode($result,JSON_PRETTY_PRINT));

        return response()->json($result);
    }


    public function outbox(Request $request)
    {

        $messages = Message::where('status',1)->where('sent_at','<',DB::raw('NOW()'))->get([
            'sender','content','type','options','media','sent_at','id','is_group'
        ]);

        if (count($messages)<1 ){
            $result = [
                'status' => 'error',
                'data' => 'data not found',
            ];
            return response()->json($result);
        }

        $result = [
            'status' => 'success',
            'data' => $messages,
        ];

        log_activity('get message outbox', json_encode($result,JSON_PRETTY_PRINT));

        return response()->json($result);
    }

    public function statusUpdate(Request $request, $id)
    {
        $data = $request->all();
        $status = isset($data['status']) ? $data['status'] : 2;
        $update = Message::where('id',$id)->update(['status' => $status]);

        if (!$update){
            $result = [
                'status' => 'error',
                'data' => 'Data gagal diupdate.',
            ];
            return response()->json($result);
        }

        $result = [
            'status' => 'success',
            'data' => $update,
        ];

        log_activity('statusUpdate', json_encode($result,JSON_PRETTY_PRINT));

        return response()->json($result);
    }

    public function getTemplate(Request $request)
    {
        $this->validate($request, [
            'msg' => 'required|string',
        ]);

        $msg= $request->query('msg');

        $result = get_template($msg);
        
        log_activity('getTemplate', json_encode($result,JSON_PRETTY_PRINT));
        return response()->json($result);
    }

    public function setTemplate(Request $request)
    {
        $this->validate($request, [
            'template' => 'required|string',
            'message' => 'required|string',
        ]);

        $data = $request->all();
        
        $messageTemplates = MessageTemplates::create([
            'template' => $data['template'],
            'message' => $data['message'],
        ]);

        if (!$messageTemplates){
            $result = [
                'status' => 'error',
            ];
            return response()->json($result);
        }

        $result = [
            'status' => 'success',
            'data' => $messageTemplates,
        ];

        log_activity('setTemplate', json_encode($result,JSON_PRETTY_PRINT));

        return response()->json($result);
    }
}
