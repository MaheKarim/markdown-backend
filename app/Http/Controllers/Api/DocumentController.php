<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $documents = Document::orderBy('updated_at', 'desc')->get();
        return response()->json($documents);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $document = Document::create($request->all());
        
        return response()->json($document, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $document = Document::findOrFail($id);
        return response()->json($document);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $document = Document::findOrFail($id);
        $document->update($request->all());
        
        return response()->json($document);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $document = Document::findOrFail($id);
        $document->delete();
        
        return response()->json(null, 204);
    }

    /**
     * Export document as PDF
     */
    public function exportPdf(string $id): JsonResponse
    {
        $document = Document::findOrFail($id);
        
        // For now, just return the document data
        // In a real implementation, you would generate PDF here
        return response()->json([
            'document' => $document,
            'message' => 'PDF export functionality would be implemented here'
        ]);
    }

    /**
     * Convert PDF to Markdown
     */
    public function convertPdf(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'pdf_content' => 'required|string',
            'filename' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // For now, just return the content as-is
        // In a real implementation, you would parse PDF here
        return response()->json([
            'markdown' => $request->pdf_content,
            'message' => 'PDF to Markdown conversion would be implemented here'
        ]);
    }
}
