<?php

namespace Tests\Feature\http\controllers\api;

use App\Post;
use App\User;
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
            $user=factory(User::class)->create();
            $response = $this->actingAs($user,'api')->json('POST','/api/posts',[
                'title' => 'titulo de prueba'
            ]);

            $response->assertJsonStructure(['id','title','created_at','updated_at'])
            ->assertJson(['title'=>'titulo de prueba'])
            ->assertStatus(201);

            $this->assertDatabaseHas('posts',['title'=>'titulo de prueba']);
        }

    public function test_validation_title()
    {
        $user=factory(User::class)->create();
        $response = $this->actingAs($user,'api')->json('POST','/api/posts',[
            'title' => ''
        ]);

        $response->assertStatus(422)
        ->assertJsonValidationErrors('title');
    }

    public function test_show(){
        //$this->withoutExceptionHandling();
        $post= factory(Post::class)->create();
        $user=factory(User::class)->create();
        $response = $this->actingAs($user,'api')->json('GET',"/api/posts/$post->id");
        $response->assertJsonStructure(['id','title','created_at','updated_at'])
        ->assertJson(['title'=>$post->title])
        ->assertStatus(200);
    }
    public function test_404_show(){
        $user=factory(User::class)->create();

        $response = $this->actingAs($user,'api')->json('GET','/api/posts/1000');
        $response->assertStatus(404);
    }

    public function test_update()
    {
        $user=factory(User::class)->create();

        //$this->withoutExceptionHandling();
        $posts=factory(Post::class)->create();
        $response = $this->actingAs($user,'api')->json('PUT',"/api/posts/$posts->id" ,[
            'title' => 'nuevo'
        ]);

        $response->assertJsonStructure(['id','title','created_at','updated_at'])
        ->assertJson(['title'=>'nuevo'])
        ->assertStatus(200);//ok

        $this->assertDatabaseHas('posts',['title'=>'nuevo']);
    }

    public function test_delete(){
        $user=factory(User::class)->create();

        //$this->withoutExceptionHandling();
        $posts=factory(Post::class)->create();
        $response=$this->actingAs($user,'api')->json('DELETE',"/api/posts/$posts->id");

        $response
        ->assertSee(null)
        ->assertStatus(204);

        $this->assertDatabaseMissing('posts',['id'=>$posts->id]);
    }

    public function test_index(){
        //$this->withoutExceptionHandling();
        $user=factory(User::class)->create();

        factory(Post::class, 5)->create();
        $response=$this->actingAs($user,'api')->json('GET','/api/posts');
        $response->assertJsonStructure([
            'data'=>
            [
                '*'=>['id','title','created_at','updated_at']
            ]
        ])->assertStatus(200);
    }

    public function test_guest(){
        $this->json('GET','/api/posts')->assertStatus(401);
        $this->json('POST','/api/posts')->assertStatus(401);
        $this->json('GET','/api/posts/1000')->assertStatus(401);
        $this->json('PUT','/api/posts/1000')->assertStatus(401);
        $this->json('DELETE','/api/posts/1000')->assertStatus(401);
    }
}
