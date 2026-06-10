<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contacts = \App\Models\Contact::all();
        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'type' => 'required|in:customer,supplier,both',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string',
        ]);

        if (empty($validated['company_name'])) {
            $validated['company_name'] = $validated['name'];
        }

        \App\Models\Contact::create($validated);
        return redirect()->route('contacts.index')->with('success', 'Contact created successfully.');
    }

    public function show(Contact $contact)
    {
        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        return view('contacts.create', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'type' => 'required|in:customer,supplier,both',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string',
        ]);

        if (empty($validated['company_name'])) {
            $validated['company_name'] = $validated['name'];
        }

        $contact->update($validated);
        return redirect()->route('contacts.index')->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully.');
    }

    public function exportExcel()
    {
        $contacts = \App\Models\Contact::all();
        $filename = "clients_contacts_" . date('Ymd_His') . ".xls";

        $xml = '<?xml version="1.0"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ';
        $xml .= 'xmlns:o="urn:schemas-microsoft-com:office:office" ';
        $xml .= 'xmlns:x="urn:schemas-microsoft-com:office:excel" ';
        $xml .= 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" ';
        $xml .= 'xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
        $xml .= '<Styles>' . "\n";
        $xml .= '<Style ss:ID="s1"><Font ss:Bold="1"/></Style>' . "\n";
        $xml .= '</Styles>' . "\n";
        $xml .= '<Worksheet ss:Name="Contacts">' . "\n";
        $xml .= '<Table>' . "\n";
        
        // Header Row
        $headers = ['ID', 'Name / Company', 'Type', 'Email', 'Phone', 'Address', 'Tax ID / VAT', 'Created Date'];
        $xml .= '<Row>' . "\n";
        foreach ($headers as $h) {
            $xml .= '<Cell ss:StyleID="s1"><Data ss:Type="String">' . htmlspecialchars($h) . '</Data></Cell>' . "\n";
        }
        $xml .= '</Row>' . "\n";

        // Data Rows
        foreach ($contacts as $c) {
            $xml .= '<Row>' . "\n";
            $data = [
                $c->id,
                $c->name,
                $c->type,
                $c->email,
                $c->phone,
                $c->address,
                $c->tax_id,
                $c->created_at ? $c->created_at->format('Y-m-d') : ''
            ];
            foreach ($data as $val) {
                $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars((string)$val) . '</Data></Cell>' . "\n";
            }
            $xml .= '</Row>' . "\n";
        }

        $xml .= '</Table>' . "\n";
        $xml .= '</Worksheet>' . "\n";
        $xml .= '</Workbook>';

        return response($xml, 200, [
            "Content-Type"        => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }
}
