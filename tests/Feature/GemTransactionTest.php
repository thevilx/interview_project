<?php

namespace Tests\Feature;

use Tests\TestCase;

use App\Models\Transaction;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GemTransactionTest extends TestCase
{
    use DatabaseTransactions; // for reverting changes after test


    public function test_can_user_buy_gem()
    {
        $player = \App\Models\Player::find(1);
        $startGem = $player->gem_count;
        $addedGem = rand( 5 , 100);

        $result = $this->post('/buy-gem' , [
            'player_id' => $player->id,
            'gem_count' => $addedGem,
            'tag' => 'buy gem'
        ]);

        $player = \App\Models\Player::find(1);
        $transaction = Transaction::find($result['TransactionID']);


        $this->assertEquals($result['status'] , 'success');
        $this->assertEquals($player->gem_count , $startGem + $addedGem);
        $this->assertEquals($transaction->gem_count , $addedGem);
        $this->assertEquals($transaction->player_id , $player->id);
    }


    public function test_can_user_buy_with_gem()
    {
        $player = \App\Models\Player::find(1);

        $startGem = $player->gem_count;
        $reducedGem = rand( 1 , $startGem);

        $result = $this->post('/buy-with-gem' , [
            'player_id' => $player->id,
            'gem_count' => $reducedGem,
            'tag' => 'buy gem'
        ]);


        $player = \App\Models\Player::find(1); // get the player data again
        $transaction = Transaction::find($result['TransactionID']);

        $this->assertEquals($result['status'] , 'success');
        $this->assertEquals($player->gem_count , $startGem - $reducedGem);
        $this->assertEquals($transaction->gem_count , $reducedGem);
        $this->assertEquals($transaction->player_id , $player->id);
    }

    public function test_user_cant_buy_item_more_than_gem_count(){

        $player = \App\Models\Player::find(1);

        $result = $this->post('/buy-with-gem' , [
            'player_id' => $player->id,
            'gem_count' => 10000,
            'tag' => 'buy gem'
        ]);


        $this->assertEquals($result['status'] , 'failed');
    }

    public function test_wrong_user_id(){

        $result = $this->post('/buy-with-gem' , [
            'player_id' => 123124124,
            'gem_count' => 100000,
            'tag' => 'buy gem'
        ]);

        $this->assertEquals($result['status'] , 'failed');
    }
}
