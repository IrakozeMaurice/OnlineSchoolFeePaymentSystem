<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\Payment;
use App\Models\Student;
use Carbon\Carbon;
use PDF;

class FinanceController extends Controller
{
    public function index()
    {
        $time = Carbon::now()->format('d-m-y H:i:m');
        // GET ALL REGISTERED STUDENTS
        $charges = Charge::all();
        $expectedPayments = Charge::sum('total_charges');
        $completedPayments = Payment::sum('amount');
        if ($expectedPayments > 0) {
            $completedPaymentsPercentage = round($completedPayments * 100 / $expectedPayments, 2);
        } else {
            $completedPaymentsPercentage = 0;
        }
        return view('financeDashboard', compact('charges', 'completedPaymentsPercentage', 'expectedPayments', 'time'));
    }

    public function showStudents()
    {
        $time = Carbon::now()->format('d-m-y H:i:m');
        $charges = Charge::all();
        return view('financeStudents', compact('time', 'charges'));
    }

    public function showPayments()
    {
        $time = Carbon::now()->format('d-m-y H:i:m');
        $payments = Payment::orderBy('updated_at', 'desc')->get();
        return view('financePayments', compact('time', 'payments'));
    }

    public function showStudentInfo(Student $student)
    {
        $time = Carbon::now()->format('d-m-y H:i:m');
        return view('financeStudentInfo', compact('time', 'student'));
    }

    public function showStudentsReport()
    {
        $time = Carbon::now()->format('d-m-y H:i:m');
        $charges = Charge::all();
        return view('reports.students.index', compact('time', 'charges'));
    }

    public function reportStudents()
    {
        request()->validate([
            'percentagePaid' => ['required']
        ]);

        $time = Carbon::now()->format('d-m-y H:i:m');
        $charges = Charge::where('percentage', '>=', request('percentagePaid'))->get();
        session()->put('percentage', request('percentagePaid'));

        return view('reports.students.index', compact('time', 'charges'));
    }

    public function students_report_pdf()
    {
        $charges = Charge::where('percentage', '>=', session('percentage'))->get();

        $pdf = PDF::loadView('reports.students.studentsList', compact('charges'))->setPaper('A4', 'portrait');

        return $pdf->download('studentsReport.pdf');
    }
}
