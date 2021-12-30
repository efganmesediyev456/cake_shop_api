<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function contact(Request $request){
        $this->validate($request,[
           "name"=>"required|string",
           "email"=>"required|email",
           "subject"=>"required|string|min:3",
           "message"=>"required|string|min:3"
        ]);

        $message=Contact::create([
            "name"=>$request->name,
            "email"=>$request->email,
            "subject"=>$request->subject,
            "message"=>$request->message,
        ]);

        return response()->json([
            "message"=>"ok"
        ],200);

//        Mail::send([],[],function($message) use($request){
//
//            $message->setBody('
//            <h1>'.$request->email.'-den mesajiniz var</h1>
//            <p style="color:black;font-size:20px;">'.$request->message.'</p>
//            ','text/html');
//
//            $message->from($request->email,$request->email);
//            $message->to('efqanesc@gmail.com','Efqan Mesediyev');
//            $message->subject($request->subject);
//        });

    }

    public function contacts(){
        $datas=[];
        $datas["not_read_counts"]=Contact::whereNull("read_at")->count();
        $datas["read_counts"]=Contact::whereNotNull("read_at")->get()->count();
        return ContactResource::collection(Contact::latest()->paginate(10))->additional($datas);
    }

    public function delete(Request $request){




        $this->validate($request,[
            "id"=>"required|integer|exists:contacts,id",
        ]);

        $contact=Contact::find($request->id);
        $contact->delete();
        return response()->json([$this->additional()],200);
    }

    public function additional(){

        $datas=[];
        $datas["not_read_counts"]=Contact::whereNull("read_at")->count();
        $datas["read_counts"]=Contact::whereNotNull("read_at")->get()->count();

        return $datas;
    }

    public function readAll(){
        Contact::whereNull("read_at")->update([
           "read_at"=>now(),
        ]);
        return response()->json([$this->additional()],200);
    }

    public function read(Request $request){
        $this->validate($request,[
            "id"=>"required|integer|exists:contacts,id",
        ]);
        if(Contact::whereNull("read_at")->whereId($request->id)->exists()){
            Contact::find($request->id)->update([
                "read_at"=>now(),
            ]);
            return response()->json($this->additional(),200);
        }

        return response()->json(["message"=>"Bele bir oxunmayan mesaj yoxdur"],422);
    }

    public function contactCounts(){
        $datas=[];
        $datas["not_read_counts"]=Contact::whereNull("read_at")->count();
        $datas["read_counts"]=Contact::whereNotNull("read_at")->get()->count();
        return $datas;
    }

}
