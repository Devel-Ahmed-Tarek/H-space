<?php
namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SimpleDataSeeder extends Seeder
{
    private $users    = [];
    private $projects = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $this->createRoles();

        // Create users with different roles
        $this->createUsers();

        // Create projects
        $this->createProjects();

        // Create tasks
        $this->createTasks();

        // Create notifications
        $this->createNotifications();
    }

    private function createRoles(): void
    {
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Project Manager']);
        Role::firstOrCreate(['name' => 'Developer']);
        Role::firstOrCreate(['name' => 'Designer']);
        Role::firstOrCreate(['name' => 'QA Tester']);
    }

    private function createUsers(): void
    {
        // Admin users
        $admin1 = User::create([
            'name'              => 'أحمد محمد',
            'email'             => 'ahmed@company.com',
            'password'          => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $admin1->assignRole('Admin');

        $admin2 = User::create([
            'name'              => 'فاطمة علي',
            'email'             => 'fatima@company.com',
            'password'          => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $admin2->assignRole('Admin');

        // Project Managers
        $pm1 = User::create([
            'name'              => 'محمد حسن',
            'email'             => 'mohamed@company.com',
            'password'          => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $pm1->assignRole('Project Manager');

        $pm2 = User::create([
            'name'              => 'سارة أحمد',
            'email'             => 'sara@company.com',
            'password'          => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $pm2->assignRole('Project Manager');

        // Developers
        $dev1 = User::create([
            'name'              => 'أميرة محمد',
            'email'             => 'amira@company.com',
            'password'          => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $dev1->assignRole('Developer');

        $dev2 = User::create([
            'name'              => 'كريم أحمد',
            'email'             => 'karim@company.com',
            'password'          => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $dev2->assignRole('Developer');

        // Designers
        $designer1 = User::create([
            'name'              => 'ليلى أحمد',
            'email'             => 'laila@company.com',
            'password'          => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $designer1->assignRole('Designer');

        // QA Testers
        $qa1 = User::create([
            'name'              => 'رانيا علي',
            'email'             => 'rania@company.com',
            'password'          => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $qa1->assignRole('QA Tester');

        // Store users for later use
        $this->users = [
            'admins'           => [$admin1, $admin2],
            'project_managers' => [$pm1, $pm2],
            'developers'       => [$dev1, $dev2],
            'designers'        => [$designer1],
            'qa_testers'       => [$qa1],
        ];
    }

    private function createProjects(): void
    {
        $projects = [
            [
                'name'               => 'تطوير موقع الشركة الإلكتروني',
                'description'        => 'تطوير موقع إلكتروني حديث ومتجاوب للشركة مع نظام إدارة المحتوى',
                'project_manager_id' => $this->users['project_managers'][0]->id,
                'start_date'         => '2024-01-15',
                'end_date'           => '2024-06-30',
                'status'             => 'In Progress',
                'is_approved'        => true,
            ],
            [
                'name'               => 'تطبيق الهاتف المحمول للعملاء',
                'description'        => 'تطوير تطبيق iOS و Android للعملاء مع ميزات متقدمة',
                'project_manager_id' => $this->users['project_managers'][1]->id,
                'start_date'         => '2024-02-01',
                'end_date'           => '2024-08-15',
                'status'             => 'Open',
                'is_approved'        => true,
            ],
            [
                'name'               => 'نظام إدارة الموارد البشرية',
                'description'        => 'تطوير نظام شامل لإدارة الموارد البشرية والرواتب',
                'project_manager_id' => $this->users['project_managers'][0]->id,
                'start_date'         => '2024-03-01',
                'end_date'           => '2024-09-30',
                'status'             => 'Open',
                'is_approved'        => false,
            ],
            [
                'name'               => 'تحسين أداء قاعدة البيانات',
                'description'        => 'تحسين الأداء الحالي لقاعدة البيانات وتطوير نظام النسخ الاحتياطي',
                'project_manager_id' => $this->users['project_managers'][1]->id,
                'start_date'         => '2024-01-01',
                'end_date'           => '2024-04-30',
                'status'             => 'Completed',
                'is_approved'        => true,
            ],
        ];

        foreach ($projects as $projectData) {
            Project::create($projectData);
        }

        $this->projects = Project::all();
    }

    private function createTasks(): void
    {
        $tasks = [
            // Project 1: تطوير موقع الشركة الإلكتروني
            [
                'title'            => 'تصميم واجهة المستخدم الرئيسية',
                'description'      => 'تصميم واجهة المستخدم الرئيسية للموقع مع التركيز على سهولة الاستخدام',
                'project_id'       => $this->projects[0]->id,
                'assigned_user_id' => $this->users['designers'][0]->id,
                'status'           => 'Done',
                'priority'         => 'High',
                'due_date'         => '2024-02-15',
            ],
            [
                'title'            => 'تطوير الصفحة الرئيسية',
                'description'      => 'تطوير الصفحة الرئيسية باستخدام React.js مع مكونات تفاعلية',
                'project_id'       => $this->projects[0]->id,
                'assigned_user_id' => $this->users['developers'][0]->id,
                'status'           => 'Done',
                'priority'         => 'High',
                'due_date'         => '2024-03-01',
            ],
            [
                'title'            => 'تطوير نظام إدارة المحتوى',
                'description'      => 'تطوير نظام إدارة المحتوى للصفحات الديناميكية',
                'project_id'       => $this->projects[0]->id,
                'assigned_user_id' => $this->users['developers'][1]->id,
                'status'           => 'In Progress',
                'priority'         => 'High',
                'due_date'         => '2024-04-15',
            ],
            [
                'title'            => 'اختبار الوظائف الأساسية',
                'description'      => 'اختبار جميع الوظائف الأساسية للموقع',
                'project_id'       => $this->projects[0]->id,
                'assigned_user_id' => $this->users['qa_testers'][0]->id,
                'status'           => 'To Do',
                'priority'         => 'Medium',
                'due_date'         => '2024-05-15',
            ],

            // Project 2: تطبيق الهاتف المحمول للعملاء
            [
                'title'            => 'تصميم واجهة التطبيق',
                'description'      => 'تصميم واجهة المستخدم للتطبيق مع التركيز على تجربة المستخدم',
                'project_id'       => $this->projects[1]->id,
                'assigned_user_id' => $this->users['designers'][0]->id,
                'status'           => 'In Progress',
                'priority'         => 'High',
                'due_date'         => '2024-03-15',
            ],
            [
                'title'            => 'تطوير تطبيق iOS',
                'description'      => 'تطوير تطبيق iOS باستخدام Swift',
                'project_id'       => $this->projects[1]->id,
                'assigned_user_id' => $this->users['developers'][0]->id,
                'status'           => 'To Do',
                'priority'         => 'High',
                'due_date'         => '2024-06-30',
            ],
            [
                'title'            => 'تطوير تطبيق Android',
                'description'      => 'تطوير تطبيق Android باستخدام Kotlin',
                'project_id'       => $this->projects[1]->id,
                'assigned_user_id' => $this->users['developers'][1]->id,
                'status'           => 'To Do',
                'priority'         => 'High',
                'due_date'         => '2024-07-15',
            ],

            // Project 3: نظام إدارة الموارد البشرية
            [
                'title'            => 'تحليل المتطلبات',
                'description'      => 'تحليل متطلبات النظام وتحديد الوظائف المطلوبة',
                'project_id'       => $this->projects[2]->id,
                'assigned_user_id' => $this->users['project_managers'][0]->id,
                'status'           => 'To Do',
                'priority'         => 'High',
                'due_date'         => '2024-04-15',
            ],

            // Project 4: تحسين أداء قاعدة البيانات
            [
                'title'            => 'تحليل الأداء الحالي',
                'description'      => 'تحليل الأداء الحالي لقاعدة البيانات وتحديد نقاط الضعف',
                'project_id'       => $this->projects[3]->id,
                'assigned_user_id' => $this->users['developers'][0]->id,
                'status'           => 'Done',
                'priority'         => 'High',
                'due_date'         => '2024-01-15',
            ],
            [
                'title'            => 'تحسين الاستعلامات',
                'description'      => 'تحسين الاستعلامات البطيئة وإضافة الفهارس المطلوبة',
                'project_id'       => $this->projects[3]->id,
                'assigned_user_id' => $this->users['developers'][1]->id,
                'status'           => 'Done',
                'priority'         => 'High',
                'due_date'         => '2024-02-15',
            ],
        ];

        foreach ($tasks as $taskData) {
            Task::create($taskData);
        }
    }

    private function createNotifications(): void
    {
        $users = User::all();
        $tasks = Task::all();

        $notificationTypes = [
            'task_assigned'        => 'تم تكليفك بمهمة جديدة',
            'task_completed'       => 'تم إكمال المهمة بنجاح',
            'project_updated'      => 'تم تحديث المشروع',
            'deadline_approaching' => 'موعد نهائي يقترب',
            'new_comment'          => 'تعليق جديد على المهمة',
        ];

        foreach ($users as $user) {
            // Create 3-8 notifications per user
            $notificationCount = rand(3, 8);

            for ($i = 0; $i < $notificationCount; $i++) {
                $type = array_rand($notificationTypes);
                $task = $tasks->random();

                $user->notifications()->create([
                    'id'      => \Illuminate\Support\Str::uuid(),
                    'type'    => 'App\Notifications\TaskNotification',
                    'data'    => [
                        'message'      => $notificationTypes[$type],
                        'task_id'      => $task->id,
                        'task_title'   => $task->title,
                        'project_name' => $task->project->name,
                        'type'         => $type,
                    ],
                    'read_at' => rand(0, 1) ? now() : null, // Some notifications are read
                ]);
            }
        }
    }

}
