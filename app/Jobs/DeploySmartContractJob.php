<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function Laravel\Prompts\error;

class DeploySmartContractJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $deploymentData;
    protected $smartContractId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($deploymentData, $smartContractId)
    {
        $this->deploymentData = $deploymentData;
        $this->smartContractId = $smartContractId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            // Kirim POST request ke Express.js API
            $response = Http::withHeaders([
                'Authorization' => env('SMART_CONTRACT_API_KEY'), // API Key dari .env
            ])
                ->timeout(300)
                ->post(env('SMART_CONTRACT_API_URL') . '/api/v1/smart-contract/deploy', [
                    'smart_contract_id' => $this->smartContractId,
                    'content_id' => $this->deploymentData['content_id'],
                    'content_name' => $this->deploymentData['content_name'],
                    'content_created_at' => $this->deploymentData['content_created_at'],
                    'content_link' => $this->deploymentData['content_link'],
                    'content_enrollment_price' => $this->deploymentData['content_enrollment_price'],
                    'content_place' => $this->deploymentData['content_place'],
                    'content_type' => $this->deploymentData['content_type'],
                    'provider' => $this->deploymentData['provider'],
                    'organization_name' => $this->deploymentData['organization_name'],
                    'user_name' => $this->deploymentData['user_name'],
                    'user_id' => $this->deploymentData['user_id'],
                ]);

            if ($response->successful()) {
                // Log sukses
                Log::channel('smartcontract')->info('[SUCCESS] Deployment process completed successfully for SmartContract ID: ' . $this->smartContractId);
            } else {
                // Log error
                Log::channel('smartcontract')->error('Failed to deploy smart contract for smart_contract_id: ' . $this->smartContractId . '. Response: ' . $response->body());

                // Optional: Update status_contract menjadi 0 (failed)
                DB::table('smart_contract')->where('id', $this->smartContractId)->update([
                    'status_contract' => 0,
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::channel('smartcontract')->error('Error deploying smart contract for smart_contract_id: ' . $this->smartContractId . '. Error: ' . $e->getMessage());

            // Update status_contract menjadi 0 (failed)
            DB::table('smart_contract')->where('id', $this->smartContractId)->update([
                'status_contract' => 0,
                'updated_at' => now(),
            ]);
        }
    }
}
