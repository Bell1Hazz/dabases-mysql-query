<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin ArticleHub',
                'email' => 'admin@articlehub.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'bio' => 'System Administrator',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@articlehub.com',
                'password' => Hash::make('password'),
                'role' => 'author',
                'bio' => 'Technology enthusiast and AI researcher',
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'michael@articlehub.com',
                'password' => Hash::make('password'),
                'role' => 'author',
                'bio' => 'UI/UX Designer passionate about user experiences',
            ],
            [
                'name' => 'Emily Rodriguez',
                'email' => 'emily@articlehub.com',
                'password' => Hash::make('password'),
                'role' => 'author',
                'bio' => 'Business strategist and entrepreneur',
            ],
            [
                'name' => 'David Kim',
                'email' => 'david@articlehub.com',
                'password' => Hash::make('password'),
                'role' => 'author',
            ],
            [
                'name' => 'Lisa Wang',
                'email' => 'lisa@articlehub.com',
                'password' => Hash::make('password'),
                'role' => 'author',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}