<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvImportController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->where(fn ($q) => $q->where('user_id', auth()->id())->orWhere('is_default', true))
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('student.csv-import', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            return back()->with('error', 'Unable to read the uploaded file.');
        }

        $categories = Category::query()
            ->where(fn ($q) => $q->where('user_id', auth()->id())->orWhere('is_default', true))
            ->get()
            ->keyBy(fn ($cat) => strtolower(trim($cat->name)) . ':' . $cat->type);

        $categoryByName = Category::query()
            ->where(fn ($q) => $q->where('user_id', auth()->id())->orWhere('is_default', true))
            ->get()
            ->keyBy(fn ($cat) => strtolower(trim($cat->name)));

        $headerRow = fgetcsv($handle);

        if (!$headerRow) {
            fclose($handle);
            return back()->with('error', 'The CSV file is empty or has no header row.');
        }

        $headers = array_map(fn ($h) => strtolower(trim($h)), $headerRow);
        $requiredHeaders = ['date', 'description', 'amount', 'type', 'category'];
        $missingHeaders = array_diff($requiredHeaders, $headers);

        if (!empty($missingHeaders)) {
            fclose($handle);
            return back()->with('error', 'Missing required CSV columns: ' . implode(', ', $missingHeaders) . '. Required: date, description, amount, type, category');
        }

        $colMap = array_flip($headers);
        $imported = 0;
        $skipped = 0;
        $errors = [];
        $rowNum = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;

            if (count($row) < 5) {
                $skipped++;
                $errors[] = "Row {$rowNum}: Insufficient columns.";
                continue;
            }

            $dateRaw = trim($row[$colMap['date']] ?? '');
            $description = trim($row[$colMap['description']] ?? '');
            $amountRaw = trim($row[$colMap['amount']] ?? '');
            $type = strtolower(trim($row[$colMap['type']] ?? ''));
            $categoryName = strtolower(trim($row[$colMap['category']] ?? ''));

            if (empty($dateRaw) || empty($description) || empty($amountRaw) || empty($type) || empty($categoryName)) {
                $skipped++;
                $errors[] = "Row {$rowNum}: Missing required data.";
                continue;
            }

            if (!in_array($type, ['income', 'expense'])) {
                $skipped++;
                $errors[] = "Row {$rowNum}: Invalid type '{$type}'. Must be 'income' or 'expense'.";
                continue;
            }

            $amount = (float) str_replace(',', '', $amountRaw);
            if ($amount <= 0) {
                $skipped++;
                $errors[] = "Row {$rowNum}: Invalid amount.";
                continue;
            }

            try {
                $date = Carbon::parse($dateRaw)->format('Y-m-d');
            } catch (\Throwable $e) {
                $skipped++;
                $errors[] = "Row {$rowNum}: Invalid date format '{$dateRaw}'.";
                continue;
            }

            $lookupKey = $categoryName . ':' . $type;
            $category = $categories[$lookupKey] ?? $categoryByName[$categoryName] ?? null;

            if (!$category) {
                $skipped++;
                $errors[] = "Row {$rowNum}: Category '{$row[$colMap['category']]}' not found.";
                continue;
            }

            Transaction::create([
                'user_id' => auth()->id(),
                'category_id' => $category->id,
                'type' => $type,
                'amount' => $amount,
                'description' => $description,
                'date' => $date,
                'recurring' => false,
                'recurring_frequency' => null,
                'notes' => 'Imported from CSV',
            ]);

            $imported++;
        }

        fclose($handle);

        $message = "CSV import complete: {$imported} transaction(s) imported successfully.";
        if ($skipped > 0) {
            $message .= " {$skipped} row(s) skipped.";
        }

        return redirect()->route('student.csv-import')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }

    public function template(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="campus_coin_import_template.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['date', 'description', 'amount', 'type', 'category']);
            fputcsv($handle, ['2026-01-15', 'Monthly Allowance from Family', '25000', 'income', 'Allowance']);
            fputcsv($handle, ['2026-01-16', 'Campus Cafe Lunch', '350', 'expense', 'Food']);
            fputcsv($handle, ['2026-01-17', 'Bus Pass Weekly', '500', 'expense', 'Transport']);
            fputcsv($handle, ['2026-01-20', 'Part-time Tutoring', '5000', 'income', 'Part-time Job']);
            fputcsv($handle, ['2026-01-22', 'Textbook Purchase', '1200', 'expense', 'Academics']);
            fputcsv($handle, ['2026-02-01', 'Hostel Rent', '8000', 'expense', 'Hostel/Rent']);
            fputcsv($handle, ['2026-02-05', 'Netflix Subscription', '1500', 'expense', 'Subscriptions']);
            fputcsv($handle, ['2026-02-10', 'Birthday Gift from Uncle', '3000', 'income', 'Gift']);
            fclose($handle);
        }, 200, $headers);
    }
}
