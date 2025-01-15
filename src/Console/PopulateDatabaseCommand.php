<?php

namespace App\Console;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Office;
use Illuminate\Support\Facades\Schema;
use Slim\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDatabaseCommand extends Command
{
    private App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('db:populate');
        $this->setDescription('Populate database');
    }

    protected function execute(InputInterface $input, OutputInterface $output ): int
    {
        $output->writeln('Populate database...');

        /** @var \Illuminate\Database\Capsule\Manager $db */
        $db = $this->app->getContainer()->get('db');

        $db->getConnection()->statement("SET FOREIGN_KEY_CHECKS=0");
        $db->getConnection()->statement("TRUNCATE `employees`");
        $db->getConnection()->statement("TRUNCATE `offices`");
        $db->getConnection()->statement("TRUNCATE `companies`");
        $db->getConnection()->statement("SET FOREIGN_KEY_CHECKS=1");

      $faker = \Faker\Factory::create();

        for ($i = 0; $i < 3; $i++) {
            $company = new Company();
            $company->name = $faker->company;
            $company->phone = $faker->phoneNumber;
            $company->email = $faker->email;
            $company->website = $faker->url;
            $company->logo = $faker->imageUrl();
            $company->created_at = $faker->dateTime;
            $company->updated_at = $faker->dateTime;
            $company->save();
        }

        for ($i = 0; $i < 3; $i++) {
            $office = new Office();
            $office->name = $faker->company;
            $office->address = $faker->streetAddress;
            $office->city = $faker->city;
            $office->zip = $faker->postcode;
            $office->country = $faker->country;
            $office->email = $faker->email;
            $office->phone = $faker->phoneNumber;
            $office->company_id = $company->id;
            $office->created_at = $faker->dateTime;
            $office->updated_at = $faker->dateTime;
            $office->save();
        }

        for ($i = 0; $i < 11; $i++) {
            $employee = new Employee();
            $employee->first_name = $faker->firstName;
            $employee->last_name = $faker->lastName;
            $employee->office_id = $faker->numberBetween(1, 8);
            $employee->email = $faker->email;
            $employee->phone = $faker->phoneNumber;
            $employee->job_title = $faker->jobTitle;
            $employee->created_at = $faker->dateTime;
            $employee->updated_at = $faker->dateTime;
            $employee->save();
        }


        $db->getConnection()->statement("update companies set head_office_id = 1 where id = 1;");
        $db->getConnection()->statement("update companies set head_office_id = 3 where id = 2;");

        $output->writeln('Database created successfully!');
        return 0;
    }
}
