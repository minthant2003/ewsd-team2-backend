<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Categories;
use App\Models\Comment;
use App\Models\Department;
use App\Models\Idea;
use App\Models\IdeaDocument;
use App\Models\Reaction;
use App\Models\ReportedIdea;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $roles = [
            ['role_name' => 'admin'],
            ['role_name' => 'manager'],
            ['role_name' => 'coordinator'],
            ['role_name' => 'staff'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // Create Departments
        $departments = [
            ['department_name' => 'Software Development'],
            ['department_name' => 'Quality Assurance'],
            ['department_name' => 'Human Resources'],
            ['department_name' => 'Finance & Accounting'],
            ['department_name' => 'Marketing & Communications'],
            ['department_name' => 'Customer Support'],
            ['department_name' => 'Research & Development'],
            ['department_name' => 'Operations'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }

        // Create Categories
        $categories = [
            ['category_name' => 'Process Improvement'],
            ['category_name' => 'Technology Enhancement'],
            ['category_name' => 'Customer Service'],
            ['category_name' => 'Workplace Culture'],
            ['category_name' => 'Product Innovation'],
            ['category_name' => 'Employee Development'],
            ['category_name' => 'Cost Optimization'],
            ['category_name' => 'Sustainability'],
        ];

        foreach ($categories as $category) {
            Categories::create($category);
        }

        // Create Academic Years
        $academicYears = [
            [
                'academic_name' => '2023-2024',
                'start_date' => '2023-09-01',
                'end_date' => '2024-08-31',
                'closure_date' => '2024-07-31',
                'final_closure_date' => '2024-08-31',
                'remark' => 'Current Academic Year',
            ],
            [
                'academic_name' => '2022-2023',
                'start_date' => '2022-09-01',
                'end_date' => '2023-08-31',
                'closure_date' => '2023-07-31',
                'final_closure_date' => '2023-08-31',
                'remark' => 'Previous Academic Year',
            ],
        ];

        foreach ($academicYears as $year) {
            AcademicYear::create($year);
        }

        // Create Users with different roles
        $users = [
            [
                'user_name' => 'john_doe',
                'email' => 'john.doe@company.com',
                'password' => Hash::make('password'),
                'phone_no' => '1234567890',
                'role_id' => 1, // Admin
                'department_id' => 1,
            ],
            [
                'user_name' => 'sarah_wilson',
                'email' => 'sarah.wilson@company.com',
                'password' => Hash::make('password'),
                'phone_no' => '1234567891',
                'role_id' => 2, // Manager
                'department_id' => 2,
            ],
            [
                'user_name' => 'michael_chen',
                'email' => 'michael.chen@company.com',
                'password' => Hash::make('password'),
                'phone_no' => '1234567892',
                'role_id' => 3, // Coordinator
                'department_id' => 3,
            ],
            [
                'user_name' => 'emma_davis',
                'email' => 'emma.davis@company.com',
                'password' => Hash::make('password'),
                'phone_no' => '1234567893',
                'role_id' => 4, // Staff
                'department_id' => 4,
            ],
            [
                'user_name' => 'alex_thompson',
                'email' => 'alex.thompson@company.com',
                'password' => Hash::make('password'),
                'phone_no' => '1234567894',
                'role_id' => 4, // Staff
                'department_id' => 5,
            ],
            [
                'user_name' => 'lisa_park',
                'email' => 'lisa.park@company.com',
                'password' => Hash::make('password'),
                'phone_no' => '1234567895',
                'role_id' => 4, // Staff
                'department_id' => 6,
            ],
            [
                'user_name' => 'david_lee',
                'email' => 'david.lee@company.com',
                'password' => Hash::make('password'),
                'phone_no' => '1234567896',
                'role_id' => 4, // Staff
                'department_id' => 7,
            ],
            [
                'user_name' => 'sophia_rodriguez',
                'email' => 'sophia.rodriguez@company.com',
                'password' => Hash::make('password'),
                'phone_no' => '1234567897',
                'role_id' => 4, // Staff
                'department_id' => 8,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        // Create Ideas
        $ideas = [
            [
                'title' => 'Implement Automated Testing Framework',
                'content' => 'Propose implementing a comprehensive automated testing framework to improve code quality and reduce manual testing time. This will include setting up CI/CD pipelines, automated unit tests, and integration tests.',
                'is_anonymous' => false,
                'view_count' => 45,
                'popularity' => 12,
                'user_id' => 4, // emma_davis
                'academic_year_id' => 1,
                'category_id' => 2,
                'remark' => 'Proposed by Finance Department',
                'report_count' => 0,
            ],
            [
                'title' => 'Improve Employee Onboarding Process',
                'content' => 'Suggest improvements to the current employee onboarding process to make it more efficient and engaging. This includes digital documentation, interactive training modules, and mentorship programs.',
                'is_anonymous' => false,
                'view_count' => 38,
                'popularity' => 15,
                'user_id' => 5, // alex_thompson
                'academic_year_id' => 1,
                'category_id' => 1,
                'remark' => 'Proposed by Marketing Department',
                'report_count' => 0,
            ],
            [
                'title' => 'Enhance Customer Support System',
                'content' => 'Propose implementing a new customer support system to better track and resolve customer issues. This will include AI-powered ticket routing, automated response suggestions, and customer satisfaction tracking.',
                'is_anonymous' => false,
                'view_count' => 52,
                'popularity' => 18,
                'user_id' => 6, // lisa_park
                'academic_year_id' => 1,
                'category_id' => 3,
                'remark' => 'Proposed by Customer Support Department',
                'report_count' => 1,
            ],
            [
                'title' => 'Green Office Initiative',
                'content' => 'Propose implementing eco-friendly practices in the office, including paperless operations, energy-efficient lighting, and recycling programs to reduce our carbon footprint.',
                'is_anonymous' => false,
                'view_count' => 42,
                'popularity' => 14,
                'user_id' => 8, // sophia_rodriguez
                'academic_year_id' => 1,
                'category_id' => 8,
                'remark' => 'Proposed by Operations Department',
                'report_count' => 0,
            ],
            [
                'title' => 'Employee Skill Development Program',
                'content' => 'Suggest creating a comprehensive skill development program that includes online courses, workshops, and certification opportunities to help employees grow in their careers.',
                'is_anonymous' => false,
                'view_count' => 35,
                'popularity' => 10,
                'user_id' => 7, // david_lee
                'academic_year_id' => 1,
                'category_id' => 6,
                'remark' => 'Proposed by R&D Department',
                'report_count' => 0,
            ],
        ];

        foreach ($ideas as $idea) {
            Idea::create($idea);
        }

        // Create Comments
        $comments = [
            [
                'desc' => 'Great idea! This would significantly improve our testing efficiency and reduce bugs in production.',
                'is_anonymous' => false,
                'user_id' => 2, // sarah_wilson
                'idea_id' => 1,
            ],
            [
                'desc' => 'I agree with this proposal. The current onboarding process needs improvement, and digital documentation would make it much smoother.',
                'is_anonymous' => false,
                'user_id' => 3, // michael_chen
                'idea_id' => 2,
            ],
            [
                'desc' => 'We should consider the cost implications of this proposal, but the benefits seem worth the investment.',
                'is_anonymous' => false,
                'user_id' => 1, // john_doe
                'idea_id' => 3,
            ],
            [
                'desc' => 'This aligns perfectly with our sustainability goals. I can help create a detailed implementation plan.',
                'is_anonymous' => false,
                'user_id' => 2, // sarah_wilson
                'idea_id' => 4,
            ],
            [
                'desc' => 'I would love to participate in this program. It would help me stay updated with the latest technologies.',
                'is_anonymous' => false,
                'user_id' => 4, // emma_davis
                'idea_id' => 5,
            ],
        ];

        foreach ($comments as $comment) {
            Comment::create($comment);
        }

        // Create Reactions
        $reactions = [
            [
                'user_id' => 2, // sarah_wilson
                'idea_id' => 1,
                'reaction' => 'like',
                'remark' => 'Supporting the automated testing initiative',
            ],
            [
                'user_id' => 3, // michael_chen
                'idea_id' => 2,
                'reaction' => 'like',
                'remark' => 'Agree with the onboarding improvements',
            ],
            [
                'user_id' => 4, // emma_davis
                'idea_id' => 2,
                'reaction' => 'unlike',
                'remark' => 'Current process works fine',
            ],
            [
                'user_id' => 5, // alex_thompson
                'idea_id' => 4,
                'reaction' => 'like',
                'remark' => 'Great initiative for sustainability',
            ],
            [
                'user_id' => 6, // lisa_park
                'idea_id' => 5,
                'reaction' => 'like',
                'remark' => 'This would be very beneficial for all employees',
            ],
        ];

        foreach ($reactions as $reaction) {
            Reaction::create($reaction);
        }

        // Create Reported Ideas
        $reportedIdeas = [
            [
                'idea_id' => 3,
                'user_id' => 4, // emma_davis
            ],
        ];

        foreach ($reportedIdeas as $reportedIdea) {
            ReportedIdea::create($reportedIdea);
        }

        // Create Idea Documents
        $ideaDocuments = [
            [
                'idea_id' => 1,
                'file_name' => 'Automated Testing Framework Proposal',
                'public_file_url' => 'documents/automated_testing_framework.pdf',
                'remark' => 'Detailed technical proposal with implementation plan',
            ],
            [
                'idea_id' => 2,
                'file_name' => 'Digital Onboarding Process Design',
                'public_file_url' => 'documents/digital_onboarding.pdf',
                'remark' => 'Process improvement proposal with workflow diagrams',
            ],
            [
                'idea_id' => 4,
                'file_name' => 'Green Office Initiative Plan',
                'public_file_url' => 'documents/green_office_plan.pdf',
                'remark' => 'Sustainability proposal with cost analysis',
            ],
        ];

        foreach ($ideaDocuments as $document) {
            IdeaDocument::create($document);
        }
    }
} 