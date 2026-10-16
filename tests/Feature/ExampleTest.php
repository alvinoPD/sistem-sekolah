<?php

namespace Tests\Feature;

use App\Models\posts;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }

    public function test_admin_can_create_post_with_uploaded_image(): void
    {
        Storage::fake('public');
        $admin = $this->createAdmin();

        $this->actingAs($admin)->get(route('posts.create'))->assertOk();

        $response = $this->actingAs($admin)->post(route('posts.store'), [
            'Foto' => UploadedFile::fake()->image('poster.jpg'),
            'Judul' => 'Kegiatan Sekolah',
            'Deskripsi' => 'Informasi kegiatan sekolah bulan ini.',
        ]);

        $response->assertRedirect(route('admin.beranda'));
        $this->assertDatabaseHas('posts', [
            'Judul' => 'Kegiatan Sekolah',
            'Deskripsi' => 'Informasi kegiatan sekolah bulan ini.',
        ]);

        $post = posts::where('Judul', 'Kegiatan Sekolah')->firstOrFail();
        Storage::disk('public')->assertExists('post/'.$post->Foto);
    }

    public function test_admin_can_update_post_and_replace_its_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('post/old.jpg', 'old-image');
        $admin = $this->createAdmin();
        $post = posts::create([
            'Foto' => 'old.jpg',
            'Judul' => 'Judul Sebelumnya',
            'Deskripsi' => 'Deskripsi awal kegiatan sekolah.',
        ]);

        $this->actingAs($admin)->get(route('posts.edit', $post))->assertOk();

        $response = $this->actingAs($admin)->put(route('posts.update', $post), [
            'Foto' => UploadedFile::fake()->image('poster-baru.jpg'),
            'Judul' => 'Judul Diperbarui',
            'Deskripsi' => 'Deskripsi terbaru kegiatan sekolah.',
        ]);

        $response->assertRedirect(route('admin.beranda'));
        $post->refresh();
        $this->assertSame('Judul Diperbarui', $post->Judul);
        $this->assertSame('Deskripsi terbaru kegiatan sekolah.', $post->Deskripsi);
        $this->assertNotSame('old.jpg', $post->Foto);
        Storage::disk('public')->assertMissing('post/old.jpg');
        Storage::disk('public')->assertExists('post/'.$post->Foto);
    }

    public function test_admin_can_delete_post_and_its_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('post/poster.jpg', 'image');
        $admin = $this->createAdmin();
        $post = posts::create([
            'Foto' => 'poster.jpg',
            'Judul' => 'Postingan Dihapus',
            'Deskripsi' => 'Deskripsi postingan yang akan dihapus.',
        ]);

        $response = $this->actingAs($admin)->delete(route('posts.destroy', $post));

        $response->assertRedirect(route('admin.beranda'));
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
        Storage::disk('public')->assertMissing('post/poster.jpg');
    }

    public function test_user_can_read_posts_but_cannot_modify_them(): void
    {
        $user = User::create([
            'name' => 'Pengguna Sekolah',
            'email' => 'user@example.test',
            'password' => 'password-sekolah',
        ]);
        $user->role = 'user';
        $user->save();
        $post = posts::create([
            'Judul' => 'Pengumuman Sekolah',
            'Deskripsi' => 'Pengumuman kegiatan sekolah minggu ini.',
        ]);

        $listingResponse = $this->actingAs($user)->get(route('user.beranda'));

        $listingResponse->assertOk();
        $listingResponse->assertSee('Pengumuman Sekolah');
        $listingResponse->assertSee(route('posts.show', $post));
        $this->get(route('posts.show', $post))->assertOk();
        $this->get(route('posts.create'))->assertForbidden();
        $this->post(route('posts.store'), [
            'Judul' => 'Postingan dari User',
            'Deskripsi' => 'Pengguna mencoba membuat postingan.',
        ])->assertForbidden();
        $this->get(route('posts.edit', $post))->assertForbidden();
        $this->put(route('posts.update', $post), [
            'Judul' => 'Judul Diubah User',
            'Deskripsi' => 'Isi diubah oleh pengguna biasa.',
        ])->assertForbidden();
        $this->delete(route('posts.destroy', $post))->assertForbidden();
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'Judul' => 'Pengumuman Sekolah',
        ]);
    }

    private function createAdmin(): User
    {
        $admin = User::create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@example.test',
            'password' => 'password-sekolah',
        ]);
        $admin->role = 'admin';
        $admin->save();

        return $admin;
    }
}
