<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function buyWithGem(Request $request){

        $itemsReq = ['player_id' , 'gem_count' , 'tag'];

        foreach($itemsReq as $req){
            $$req = $request->{$req};
        }

        try{
            $player = Player::find($player_id);

            if ($player->canPay($gem_count)){

                $player->gem_count -= $gem_count;
                $player->save();

                $transaction = new Transaction();
                $transaction->tag = $tag;
                $transaction->gem_count = $gem_count;
                $transaction->player_id = $player_id;
                $transaction->transaction_type = 'buyWithGem';
                $transaction->save();

                return response()->json([ // we can use response later for production
                    'status' => 'success',
                    'message' => 'transaction success',
                    'TransactionID' => $transaction->id
                ]);

            }else{
                return response()->json([
                    'status' => 'failed',
                    'message' => 'not enough gem'
                ]);
            }


        }  catch (\Throwable $e){
            return response()->json([
                'status' => 'failed',
                'message' => 'there is a problem !'
            ]);
        }


    }

    public function buyGem(Request $request){

        $itemsReq = ['player_id' , 'gem_count' , 'tag'];

        foreach($itemsReq as $req){
            $$req = $request->{$req};
        }

        try{

            Player::find($player_id)->increment('gem_count' , $gem_count);

            $transaction = new Transaction;
            $transaction->gem_count = $gem_count;
            $transaction->player_id = $player_id;
            $transaction->tag = $tag;
            $transaction->transaction_type = 'buyGem';
            $transaction->save();

            return response()->json([ // TODO : we can use response later for production
                'status' => 'success',
                'message' => 'transaction success',
                'TransactionID' => $transaction->id
            ]);

        }
        catch(\Throwable $e){

            // TODO : we can log error later in here
            return response()->json([
                'status' => 'failed',
                'message' => "There Is a problem with buying gem"
            ]);
        }
    }


}
