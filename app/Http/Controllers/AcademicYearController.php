<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Models\AcademicYear;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AcademicYearController extends Controller
{
    // Get current AY
    public static function getCurrentAcademicYear()
    {
        $currentAY = null;
        try {
            $currentDatetime = Carbon::now();
            $currentAY = AcademicYear::where("start_date", "<=", $currentDatetime)
                ->where("end_date", ">=", $currentDatetime)
                ->first();
        } catch (\Exception $e) {
            $currentAY = null;
        }
        return $currentAY;
    }

    // Get all academic years
    public function getAcademicYears(Request $request){
        try{
            $academicYears = AcademicYear::all();
            $camelObjList = [];
            foreach ($academicYears as $academicYear) {
                $camelObjList[] = $this->formatCamelCase($academicYear);
            }
            return ApiResponseClass::sendResponse($camelObjList, 'Academic years fetched successfully');
        }catch(\Exception $e){
            return ApiResponseClass::rollback($e, 'Failed to fetch Academic years');
        }
    }

    // Get single academic year by id
    public function getAcademicYearById($id)
    {
        try {
            $academicYear = AcademicYear::find($id);

            if (!$academicYear) {
                return ApiResponseClass::sendResponse(null, 'Academic year not found', 404);
            }
            $camelObj = $this->formatCamelCase($academicYear);
            return ApiResponseClass::sendResponse($camelObj, 'Academic year fetched successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to fetch academic year');
        }
    }

    // Create academic year
    public function createAcademicYear(Request $request){
        try{
            $validationFailObj = $this->validateAcademicYear($request);
            if($validationFailObj){
                return $validationFailObj;
            }

            $formattedData = $this->formatAcademicYearForDb($request);

            $academicYear = AcademicYear::create($formattedData);
            $camelObj = $this->formatCamelCase($academicYear);
            return ApiResponseClass::sendResponse($camelObj, 'Academic year created successfully', 201);
        }catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to create academic year');
        }
    }

    // Update academic year
    public function updateAcademicYear($id, Request $request)
    {
        try {
            $academicYear = AcademicYear::find($id);

            if (!$academicYear) {
                return ApiResponseClass::sendResponse(null, 'Academic year not found', 404);
            }

            $validationFailObj = $this->validateAcademicYear($request);
            if ($validationFailObj) {
                return $validationFailObj;
            }
            $formattedData = $this->formatAcademicYearForDb($request);

            $academicYear->update($formattedData);
            $camelObj = $this->formatCamelCase($academicYear);
            return ApiResponseClass::sendResponse($camelObj, 'Academic year updated successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to update academic year');
        }
    }

    // Delete academic year by id
    public function deleteAcademicYear($id)
    {
        try {
            $academicYear = AcademicYear::find($id);

            if (!$academicYear) {
                return ApiResponseClass::sendResponse(null, 'Academic year not found', 404);
            }

            if ($academicYear->ideas()->exists()) {
                return ApiResponseClass::sendResponse(null, 'Cannot delete academic year. It is assigned to ideas.', 400);
            }

            $academicYear->delete();

            return ApiResponseClass::sendResponse(null, 'Academic year deleted successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to delete academic year');
        }
    }

    private function validateAcademicYear($request){
        $validator = Validator::make($request->all(), [
            'academicName' => 'required',
            'startDate' => 'required|date|before:endDate',
            'endDate' => 'required|date|after:startDate',
            'closureDate' => 'required|date|after:startDate|before:endDate|before:finalClosureDate',
            'finalClosureDate' => 'required|date|after:startDate|before:endDate|after:closureDate',
        ],[
            'academicName.required' => 'The academic name is required.',

            'startDate.required' => 'The start date is required.',
            'startDate.date' => 'The start date must be a valid date.',
            'startDate.before' => 'The start date must be before the end date.',

            'endDate.required' => 'The end date is required.',
            'endDate.date' => 'The end date must be a valid date.',
            'endDate.after' => 'The end date must be after the start date.',

            'closureDate.required' => 'The closure date is required.',
            'closureDate.date' => 'The closure date must be a valid date.',
            'closureDate.after' => 'The closure date must be after the start date.',
            'closureDate.before' => 'The closure date must be between the start and end date and before the final closure date.',

            'finalClosureDate.required' => 'The final closure date is required.',
            'finalClosureDate.date' => 'The final closure date must be a valid date.',
            'finalClosureDate.after' => 'The final closure date must be after the start date and the closure date.',
            'finalClosureDate.before' => 'The final closure date must be between the start and end date.',
        ]);

        if ($validator->fails()) {
            return ApiResponseClass::sendResponse($validator->errors(), "Validation errors", 400);
        }
        return null;
    }

    // Format academic year data for database, ensure dates are in YYYY-MM-DD format
    private function formatAcademicYearForDb($request)
    {
        return [
            "academic_name" => $request->academicName,
            "start_date" => Carbon::parse($request->startDate)->format('Y-m-d'),
            "end_date" => Carbon::parse($request->endDate)->format('Y-m-d'),
            "closure_date" => Carbon::parse($request->closureDate)->format('Y-m-d'),
            "final_closure_date" => Carbon::parse($request->finalClosureDate)->format('Y-m-d'),
            "remark" => $request->remark
        ];
    }

    // Format academic year data in camel case
    private function formatCamelCase($obj)
    {
        return [
            'id' => $obj->id,
            'academicName' => $obj->academic_name,
            'startDate' => $obj->start_date,
            'endDate' => $obj->end_date,
            'closureDate' => $obj->closure_date,
            'finalClosureDate' => $obj->final_closure_date,
            'remark' => $obj->remark,
            'status' => $this->calculateStatus($obj),
            'createdAt' => Carbon::parse($obj->created_at)->format('Y-m-d H:i:s'),
            'updatedAt' => Carbon::parse($obj->updated_at)->format('Y-m-d H:i:s'),
        ];
    }

    private function calculateStatus($academicYear)
    {
        $now = Carbon::now();
        $startDate = Carbon::parse($academicYear->start_date);
        $endDate = Carbon::parse($academicYear->end_date);
        $closureDate = Carbon::parse($academicYear->closure_date);
        $finalClosureDate = Carbon::parse($academicYear->final_closure_date);

        if ($now->isBefore($startDate)) {
            return 'future';
        }

        if ($now->isAfter($endDate)) {
            return 'past';
        }

        if ($now->isAfter($finalClosureDate)) {
            return 'final_closed';
        }

        if ($now->isAfter($closureDate)) {
            return 'closed';
        }

        return 'current';
    }

    public function downloadIdeasCsv($id)
    {
        try {
            $academicYear = AcademicYear::find($id);
            if (!$academicYear) {
                return ApiResponseClass::sendResponse(null, 'Academic year not found', 404);
            }

            $ideas = $academicYear->ideas()
                ->join('users', 'ideas.user_id', '=', 'users.id')
                ->join('categories', 'ideas.category_id', '=', 'categories.id')
                ->select(
                    'ideas.id',
                    'ideas.title',
                    'ideas.content',
                    'ideas.is_anonymous',
                    'ideas.view_count',
                    'ideas.popularity',
                    'users.user_name',
                    'categories.category_name',
                    'ideas.created_at'
                )
                ->get();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="ideas-' . $id . '.csv"',
            ];

            $callback = function() use ($ideas) {
                $file = fopen('php://output', 'w');
                
                fputcsv($file, [
                    'ID',
                    'Title',
                    'Content',
                    'Anonymous',
                    'Views',
                    'Popularity',
                    'Author',
                    'Category',
                    'Created At'
                ]);

                foreach ($ideas as $idea) {
                    fputcsv($file, [
                        $idea->id,
                        $idea->title,
                        $idea->content,
                        $idea->is_anonymous ? 'Yes' : 'No',
                        $idea->view_count,
                        $idea->popularity,
                        $idea->is_anonymous ? 'Anonymous' : $idea->user_name,
                        $idea->category_name,
                        Carbon::parse($idea->created_at)->format('Y-m-d H:i:s')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to download ideas CSV');
        }
    }

    public function downloadSubmittedFiles($id)
    {
        try {
            $academicYear = AcademicYear::find($id);
            if (!$academicYear) {
                return ApiResponseClass::sendResponse(null, 'Academic year not found', 404);
            }

            $ideas = $academicYear->ideas()->with('ideaDocuments')->get();
            
            $tempDirName = 'temp/' . uniqid();
            Storage::makeDirectory($tempDirName);
            $tempDir = Storage::path($tempDirName);

            foreach ($ideas as $idea) {
                $ideaDirName = $tempDirName . '/idea_' . $idea->id;
                Storage::makeDirectory($ideaDirName);

                foreach ($idea->ideaDocuments as $document) {
                    if (Storage::disk('public')->exists($document->file_name)) {
                        $fileContent = Storage::disk('public')->get($document->file_name);
                        Storage::put($ideaDirName . '/' . basename($document->file_name), $fileContent);
                    }
                }
            }

            $zipFileName = "submitted-files-{$id}.zip";
            $zipPath = Storage::path($tempDirName . '/' . $zipFileName);
            
            // TODO: this might work only with Docker
            $command = "cd " . escapeshellarg(Storage::path($tempDirName)) . " && zip -r " . escapeshellarg($zipFileName) . " .";
            exec($command);

            $fileContent = Storage::get($tempDirName . '/' . $zipFileName);

            Storage::deleteDirectory($tempDirName);

            return response($fileContent, 200, [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'attachment; filename="' . $zipFileName . '"',
            ]);

        } catch (\Exception $e) {
            if (isset($tempDirName)) {
                Storage::deleteDirectory($tempDirName);
            }
            return ApiResponseClass::rollback($e, 'Failed to download submitted files');
        }
    }
}
