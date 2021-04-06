<?php

namespace Tests\Feature\http\controllers\api;

use App\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_store()
    {
        //$this->withoutExceptionHandling();
        $response = $this->json('POST','/api/posts',[
            'title' => 'titulo de prueba'
        ]);

        $response->assertJsonStructure(['id','title','created_at','updated_at'])
        ->assertJson(['title'=>'titulo de prueba'])
        ->assertStatus(201);

        $this->assertDatabaseHas('posts',['title'=>'titulo de prueba']);
    }

    public function test_validation_title()
    {
        $response = $this->json('POST','/api/posts',[
            'title' => ''
        ]);

        $response->assertStatus(422)
        ->assertJsonValidationErrors('title');
    }

    public function test_show(){
        //$this->withoutExceptionHandling();
        $post= factory(Post::class)->create();

        $response = $this->json('GET',"/api/posts/$post->id");
        $response->assertJsonStructure(['id','title','created_at','updated_at'])
        ->assertJson(['title'=>$post->title])
        ->assertStatus(200);
    }
    public function test_404_show(){

        $response = $this->json('GET','/api/posts/1000');
        $response->assertStatus(404);
    }
}
