<?php

namespace App\Console\Commands;

use App\Services\AgeRestrictedSettingService;
use App\Services\CategoryService;
use Exception;
use Illuminate\Console\Command;

class UpdateCategoryAgeRestrictedAndSimulatedContentValues extends Command
{
    protected $categoryService;
    protected $ageRestrictedService;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        AgeRestrictedSettingService $ageRestrictedService,
        CategoryService $categoryService
    ) {
        $this->categoryService      = $categoryService;
        $this->ageRestrictedService = $ageRestrictedService;
        parent::__construct();
    }
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ysg:update_category_age_restricted';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update category age restricted';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('START COMMAND');

        $this->updateCategoryAgeRestrictedValue();
        $this->ageRestrictedSimulationContent();

        $this->info('END COMMAND');
    }

    private function updateCategoryAgeRestrictedValue() {
        $categories = $this->categoryService->getAllWithConditions(['is_age_restricted' => 1]);
        foreach ($categories as $category) {
            $data['is_age_restricted'] = 18;
            $this->categoryService->update($category->id, $data);
            $this->info('update category successfully '. $category->name);
        }
    }

    private function ageRestrictedSimulationContent() {

        $generateContents = [
            18 => [
                'top_content' => '
                    <p style="text-align: center;">
                        <span style="color: rgb(7, 55, 99);">To buy alcohol, you must be 18 years of age or older.</span>
                    </p>
                    <p style="text-align: center;"><span style="color: rgb(7, 55, 99); font-size: 14px;">By clicking the "Yes" button below, you declare that you are of legal age to purchase alcohol from this site.</span></p>
                    <p style="text-align: center;"><br></p><p style="text-align: center;"><strong><span style="font-size: 18px; color: rgb(7, 55, 99);">Are you 18 years of Age or Older ?</span></strong> </p>',
                'body_content' => '
                    <p style="text-align: center;"><strong><span style="color: rgb(255, 0, 0); font-size: 18px;">Warning Notice</span></strong></p>
                    <p style="text-align: center;">
                        <span style="font-size: 14px; color: rgb(7, 55, 99);">
                            It is an offence for any person under 18 to purchase or attempt to purchase alcohol.
                            A person found guilty of this offence shall be liable on conviction to a fine up to 
                        </span>
                    <span style="color: rgb(7, 55, 99);"><strong style="">S$10,000.</strong></span>
                </p>'
            ],
            21 => [
                'top_content' => '
                    <p style="text-align: center;">
                        <span style="color: rgb(7, 55, 99);">You must be 21 years of age or older to view this content.</span>
                    </p>
                    <p style="text-align: center;">
                        <span style="color: rgb(7, 55, 99);"> By clicking the "Yes" button, you declare you are of legal age.</span>
                    </p>
                    <p style="text-align: center;"></p><br>
                    <p style="text-align: center;">
                        <span style="font-size: 16px; color: rgb(7, 55, 99);"><strong style="">Are you 21 years of Age or Older?</strong>
                        </span>
                    </p>',
                'body_content' => ''
            ],
        ];

        foreach ($generateContents as $key => $content) {
            $data['age'] = $key;
            $data['top_content']  = $content['top_content'];
            $data['body_content'] = $content['body_content'];

            $this->ageRestrictedService->create($data);
            $this->info('Content has been inserted successfully ' . $data['age']);
        }
    }
}