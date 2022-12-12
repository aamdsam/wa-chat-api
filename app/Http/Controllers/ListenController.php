<?php

namespace App\Http\Controllers;

use App\Models\MessageTemplates;
use Illuminate\Http\Request;

class ListenController extends Controller
{
    public function checkMessage(Request $request)
    {
        $this->validate($request, [
            'sender' => 'required|string',
            'type' => 'required|string',
            'input' => 'required|string',
        ]);

        $data = $request->all();
        
        $input = strtolower($data['input']);
        if ($input==='help'){
            $template = get_template('get_help');

            if ($template['status']=='success'){
                $result = $template;
            }
        }

        return response()->json($result);
    }
}
