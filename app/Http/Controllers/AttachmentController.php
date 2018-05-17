<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function createAttachment(Request $request)
    {
        if ($request->file('attachment')->isValid())
        {
            $file = $request->file('attachment')->move('../storage/attachments',
                time().$request->file('attachment')->getClientOriginalName());
        }

        return response()->json(['file' => $file->getRealPath()]);
    }

    public function retrieveAttachment(string $id)
    {
        return response()->download('../storage/attachments/'.$id);
    }
}
