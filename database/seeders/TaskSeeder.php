<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Phase;
use App\Models\Task;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createTasksForUser('Luke Skywalker', 3);
        $this->createTasksForUser('Princess Leia Organa', 3);
        $this->createTasksForUser('Han Solo', 3);
        $this->createTasksForUser('Darth Vader', 3);
        $this->createTasksForUser('Chewbacca', 3);
    }

    /**
     * Create tasks for a specific user.
     *
     * @param string $userName
     * @param int $count
     * @return void
     */
    private function createTasksForUser(string $userName, int $count): void
    {
        $user = User::whereName($userName)->first();
        $phases = Phase::where('name', "!=", 'completion')->get()->shuffle();
        $currentDate = Carbon::now();

        Task::factory()
            ->count($count)
            ->sequence(
                ['name' => 'Try not to lose your lightsaber this time.'],
                ['name' => 'Attend a rebel strategy meeting and avoid dozing off.'],
                ['name' => 'Convince Yoda to give you a day off from Jedi training.'],
                ['name' => 'Negotiate with potential allies without rolling your eyes.'],
                ['name' => 'Brief Rebel spies without revealing your secret crush on Han.'],
                ['name' => 'Try diplomacy with planets that still think Jar Jar is funny.'],
                ['name' => 'Fix the Falcon\'s hyperdrive (again).'],
                ['name' => 'Outsmart Imperial patrols while smuggling space \'stuff.\''],
                ['name' => 'Remind Chewie to lower the ship\'s thermostat – It\'s not Hoth in here!'],
                ['name' => 'Hunt Rebel spies and resist force-choking them.'],
                ['name' => 'Attend a meeting with Palpatine without yawning audibly.'],
                ['name' => 'Attend Sith sensitivity training session to work on your anger management.'],
                ['name' => 'Keep the Falcon from falling apart mid-hyperspace jump.'],
                ['name' => 'Help Han escape a bounty hunter ambush without roaring too much.'],
                ['name' => 'Book Wookiee vocal lessons – surprise opera performance for Han.'],
            )
            ->state(new Sequence(
                fn (Sequence $sequence) => [
                    'user_id' => $user->id,
                    'phase_id' => $phases->pop()->id,
                    'due_date' => $this->generateRandomDueDate()
                ],
            ))
            ->create();
    }

    private function generateRandomDueDate(): string
    {
        $currentDate = Carbon::now();

        // Randomly decide whether to set due date in the past (between -30 to -1 days)
        // or in the future (1-30 days from the current date).
        if (rand(0, 1) === 1) {
            $dueDate = $currentDate->subDays(rand(1, 30));
        } else {
            $dueDate = $currentDate->addDays(rand(1, 30));
        }

        return $dueDate->toDateString();
    }
}
