<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. PM Account
        $pm = User::create([
            'name' => 'Project Manager',
            'username' => 'pm',
            'email' => 'pm@projectpulse.local',
            'password' => Hash::make('password'),
            'role' => 'pm',
        ]);

        // 2. Developer Accounts
        $dev1 = User::create([
            'name' => 'Developer One',
            'username' => 'developer_1',
            'email' => 'dev1@projectpulse.local',
            'password' => Hash::make('password'),
            'role' => 'developer',
        ]);

        $dev2 = User::create([
            'name' => 'Developer Two',
            'username' => 'developer_2',
            'email' => 'dev2@projectpulse.local',
            'password' => Hash::make('password'),
            'role' => 'developer',
        ]);

        $dev3 = User::create([
            'name' => 'Developer Three',
            'username' => 'developer_3',
            'email' => 'dev3@projectpulse.local',
            'password' => Hash::make('password'),
            'role' => 'developer',
        ]);

        // 3. Client Accounts
        $client1 = User::create([
            'name' => 'Client One (Apex Corp)',
            'username' => 'client_1',
            'email' => 'client1@apex.local',
            'password' => Hash::make('password'),
            'role' => 'client',
        ]);

        $client2 = User::create([
            'name' => 'Client Two (Zenith Ltd)',
            'username' => 'client_2',
            'email' => 'client2@zenith.local',
            'password' => Hash::make('password'),
            'role' => 'client',
        ]);

        $client3 = User::create([
            'name' => 'Client Three (Nexus Media)',
            'username' => 'client_3',
            'email' => 'client3@nexus.local',
            'password' => Hash::make('password'),
            'role' => 'client',
        ]);

        // 4. Designer Accounts
        $designer1 = User::create([
            'name' => 'Designer One',
            'username' => 'designer_1',
            'email' => 'designer1@projectpulse.local',
            'password' => Hash::make('password'),
            'role' => 'designer',
        ]);

        $designer2 = User::create([
            'name' => 'Designer Two',
            'username' => 'designer_2',
            'email' => 'designer2@projectpulse.local',
            'password' => Hash::make('password'),
            'role' => 'designer',
        ]);

        // 5. QA Engineer Accounts
        $qa1 = User::create([
            'name' => 'QA Engineer One',
            'username' => 'qa_1',
            'email' => 'qa1@projectpulse.local',
            'password' => Hash::make('password'),
            'role' => 'qa',
        ]);

        $qa2 = User::create([
            'name' => 'QA Engineer Two',
            'username' => 'qa_2',
            'email' => 'qa2@projectpulse.local',
            'password' => Hash::make('password'),
            'role' => 'qa',
        ]);

        // 6. Create Seed Projects
        $proj1 = Project::create([
            'title' => 'E-Commerce Platform Redesign',
            'description' => 'Comprehensive redesign of customer storefront, payment gateway integrations, and order management.',
            'proposal_file_path' => 'proposals/ecommerce_redesign_v1.pdf',
            'client_id' => $client1->id,
            'status' => 'In-Progress',
            'ui_link' => 'https://figma.com/file/ecommerce-redesign-mockup',
            'ui_status' => 'Accepted',
        ]);

        // Assign PM, Developers, Designers, QA to Project 1 via project_user pivot table
        $proj1->users()->attach([
            $pm->id,
            $dev1->id,
            $dev2->id,
            $designer1->id,
            $qa1->id,
        ]);

        $proj2 = Project::create([
            'title' => 'Mobile Banking Client Portal',
            'description' => 'Secure mobile responsive banking portal with biometric authentication and statement downloads.',
            'proposal_file_path' => 'proposals/banking_portal_specs.pdf',
            'client_id' => $client2->id,
            'status' => 'UI Pending',
            'ui_link' => 'https://figma.com/file/banking-portal-wireframes',
            'ui_status' => 'Pending',
        ]);

        $proj2->users()->attach([
            $pm->id,
            $dev2->id,
            $dev3->id,
            $designer2->id,
            $qa2->id,
        ]);

        // 7. Create Tasks for Projects
        $task1 = Task::create([
            'project_id' => $proj1->id,
            'title' => 'User Authentication & JWT Tokens',
            'description' => 'Implement secure authentication endpoints, refresh token rotation, and password reset flows.',
            'assigned_to' => $dev1->id,
            'status' => 'QA Approved',
        ]);

        $task2 = Task::create([
            'project_id' => $proj1->id,
            'title' => 'Stripe & PayPal Payment Gateway Integration',
            'description' => 'Connect to payment sandbox APIs, support credit cards and webhooks for payment confirmations.',
            'assigned_to' => $dev2->id,
            'status' => 'In Progress',
        ]);

        $task3 = Task::create([
            'project_id' => $proj1->id,
            'title' => 'Product Catalog Filter UI Components',
            'description' => 'Design high-converting filter drawer and product grid with instant search capabilities.',
            'assigned_to' => $designer1->id,
            'status' => 'Completed',
        ]);

        $task4 = Task::create([
            'project_id' => $proj1->id,
            'title' => 'End-to-End Checkout Security Audit',
            'description' => 'Perform vulnerability scanning, test XSS, CSRF, and SQLi attack vectors on checkout.',
            'assigned_to' => $qa1->id,
            'status' => 'Pending',
        ]);

        // 8. Create Comments
        Comment::create([
            'project_id' => $proj1->id,
            'user_id' => $client1->id,
            'message' => 'The UI wireframes look terrific. Excited to test the checkout integration!',
            'task_id' => $task3->id,
        ]);

        Comment::create([
            'project_id' => $proj1->id,
            'user_id' => $pm->id,
            'message' => 'Payment gateway sandbox testing is scheduled for Thursday. Sarah and Alex are coordinating endpoints.',
            'task_id' => $task2->id,
        ]);

        Comment::create([
            'project_id' => $proj1->id,
            'user_id' => $dev1->id,
            'message' => 'JWT auth module passed QA testing and code review. Merged into staging.',
            'task_id' => $task1->id,
        ]);
    }
}
