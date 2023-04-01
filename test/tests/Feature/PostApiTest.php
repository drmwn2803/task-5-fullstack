<?php

namespace Tests\Feature;

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Post;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    private $accessToken = '';

    protected function setUp(): void
    {
        parent::setUp();

        $response = $this->json('POST', '/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password'
        ]);
        $this->accessToken = $response->json()['access_token'];
    }
    
    public function test_can_create_post()
    {
        $postData = [
            'title' => 'Test Post',
            'content' => 'This is a test post',
            'category_id' => 1
        ];
    
        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    
        $response = $this->withHeaders($headers)->json('POST', '/api/v1/posts', $postData);
    
        $response->assertStatus(201)
            ->assertJson([
                'title' => 'Test Post',
                'content' => 'This is a test post',
                'category_id' => 1
            ]);
    }
    
    public function test_can_list_posts()
    {
        $posts = Post::factory()->count(3)->create();
    
        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    
        $response = $this->withHeaders($headers)->json('GET', '/api/v1/posts');
    
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'content',
                        'category_id',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'links',
                'meta'
            ]);
    }
    
    public function test_can_show_post()
    {
        $post = Post::factory()->create();
    
        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    
        $response = $this->withHeaders($headers)->json('GET', '/api/v1/posts/' . $post->id);
    
        $response->assertStatus(200)
            ->assertJson([
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'category_id' => $post->category_id,
            ]);
    }
    
    public function test_can_update_post()
    {
        $post = Post::factory()->create();
    
        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    
        $updatedPostData = [
            'title' => 'Updated Test Post',
            'content' => 'This is an updated test post',
            'category_id' => 2
        ];
    
        $response = $this->withHeaders($headers)->json('PUT', '/api/v1/posts/' . $post->id, $updatedPostData);
    
        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Updated Test Post',
                'content' => 'This is an updated test post',
                'category_id' => 2
            ]);
    }
    
    public function test_can_delete_post()
    {
        $post = Post::factory()->create();
    
        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    
        $response = $this->withHeaders($headers)->json('DELETE', '/api/v1/posts/' . $post->id);
    
        $response->assertStatus
        // add pagination parameter
        $response = $this->json('GET', '/api/v1/posts?page=1');
        $response->assertStatus(200);
    
        // check if the response structure is correct
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'content',
                    'category' => [
                        'id',
                        'name'
                    ],
                    'created_at',
                    'updated_at'
                ]
            ],
            'links' => [
                'first',
                'last',
                'prev',
                'next'
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'links' => [
                    '*' => [
                        'url',
                        'label',
                        'active'
                    ]
                ],
                'path',
                'per_page',
                'to',
                'total'
            ]
        ]);
    }
    
    public function test_can_create_post()
    {
        $post = Post::factory()->make();
    
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
        ])->json('POST', '/api/v1/posts', $post->toArray());
    
        $response->assertStatus(201);
    
        // check if the post is created successfully
        $response->assertJson([
            'message' => 'Post created successfully.',
            'data' => [
                'title' => $post->title,
                'content' => $post->content,
                'category_id' => $post->category_id,
            ],
        ]);
    }
    
    public function test_can_show_post()
    {
        $post = Post::factory()->create();
    
        $response = $this->json('GET', '/api/v1/posts/' . $post->id);
        $response->assertStatus(200);
    
        // check if the response data is correct
        $response->assertJson([
            'data' => [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'category' => [
                    'id' => $post->category_id,
                    'name' => $post->category->name
                ]
            ]
        ]);
    }
    
    public function test_can_update_post()
    {
        $post = Post::factory()->create();
        $updatedPost = Post::factory()->make();
    
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
        ])->json('PUT', '/api/v1/posts/' . $post->id, $updatedPost->toArray());
    
        $response->assertStatus(200);
    
        // check if the post is updated successfully
        $response->assertJson([
            'message' => 'Post updated successfully.',
            'data' => [
                'title' => $updatedPost->title,
                'content' => $updatedPost->content,
                'category_id' => $updatedPost->category_id,
            ],
        ]);
    }
    
    public function test_can_delete_post()
    {
        $post = Post::factory()->create();
    
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
        ])->json('DELETE', '/api/v1/posts/' . $post->id);
    
        $response->assertStatus(204);
    
        // check if the post is deleted successfully
        $this->assertDatabaseMissing('posts', [
            'id' => $deletedPost->id
        ]);
        }
        
        }
    }
    