<?php

namespace Tests\Unit;

use App\Models\Box;
use App\Models\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $users = User::factory(50)->create();

        $boxIds = Box::query()->orderByDesc('created_at')->limit(10)->pluck('id');
        $ifSend = true;
        foreach ($users as $user){
            $response = $this->actingAs($user)->post('/api/rooms/enter_room', ['owner_id' => 2160, 'room_pass' => 111111]);

        }
        // Make an authorized request and perform assertions
//        $response = $this->actingAs($user)->post('/api/rooms/enter_room', ['owner_id' => 2160, 'room_pass' => 111111]);
//        $response->assertStatus(200);
    }


    public function test_boxes()
    {
        $users = User::query()->orderByDesc('created_at')->limit(50)->get();

        $boxIds = Box::query()->orderByDesc('created_at')->limit(10)->pluck('id')->toArray();
        $ifSend = true;
        //        foreach ($users as $user){
        //            $response = $this->actingAs($user)->post('/api/rooms/enter_room', ['owner_id' => 2160, 'room_pass' => 111111]);

        if ($ifSend){
            $user = $users->first();
            $user->di = 99999999999999;
            $user->save();

            foreach ($boxIds as $boxId){
                $response = $this->actingAs($user)->post('/api/box/send', ['box_id' => $boxId, 'room_uid' => 2160, 'user_num' => 7]);
            }

            foreach ($boxIds as $boxId){
                foreach ($users as $user)
                $response = $this->actingAs($user)->post('/api/box/pick', ['bid' => $boxId]);

            }
        }


        //        }
        // Make an authorized request and perform assertions
        //        $response = $this->actingAs($user)->post('/api/rooms/enter_room', ['owner_id' => 2160, 'room_pass' => 111111]);
        //        $response->assertStatus(200);
    }
}
