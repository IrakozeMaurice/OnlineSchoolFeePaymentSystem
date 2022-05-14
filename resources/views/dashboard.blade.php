@section('extra-css')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
@endsection
@section('extra-js')
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
@endsection
@section('username')
  {{ $student->names }}
@endsection
@section('sweetalertjs')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
  <script type="text/javascript">
    $('.show_confirm').click(function(event) {
      var form = $(this).closest("form");
      var name = $(this).data("name");
      event.preventDefault();
      swal({
          title: `Confirm transaction`,
          text: "are you sure you want to proceed with this transaction?",
          icon: "warning",
          buttons: true,
          //   dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) {
            form.submit();
            $("#payBtn").attr("disabled", true);
          }
        });
    });
  </script>
@endsection
<x-app-layout>
  <x-slot name="header">
  </x-slot>
  <div class="py-12">
    <div class="row">
      <div class="col-md-4">
        <h5 class="pl-4">Student Id: <span
            class="badge badge-pill badge-success">{{ $student->student_id }}</span></h5>
      </div>
      <div class="col-md-6">
        <h5>Student names: <span
            class="badge badge-pill badge-primary">{{ $student->names }}</span>
        </h5>
      </div>
      <div class="col-md-2">{{$charge->percentage >= 100 ? 'PAID' : $charge->percentage.'%'}}</div>
    </div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
          <!-- Nav tabs -->
          <ul class="nav nav-tabs">
            <li class="nav-item">
              <a class="nav-link active" data-toggle="tab" href="#home">Pay School Fees</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-toggle="tab" href="#menu1">View Registration</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-toggle="tab" href="#menu2">Payment Summary</a>
            </li>
          </ul>

          <!-- Tab panes -->
          <div class="tab-content mt-6">
            <div class="tab-pane active container" id="home">
              <div class="text-center mt-4" style="width: 60%;margin:auto;">
                <!-- Validation Errors -->
                <x-auth-validation-errors class="mb-4" :errors="$errors" />
                @if (session('success'))
                  <div class="alert alert-success">
                    <h5 class="text-center">{{ session('success') }}</h5>
                    <p class="text-center">{{ session('message') }}</p>
                  </div>
                @endif
                <form action="{{ route('payments.store') }}" method="POST">
                  @csrf
                  <div class="row">
                    <div class="col-md-6">
                      <h5 class="text-center">Fill in bank slip</h5>
                      <hr>
                      <div class="form-group">
                        <select class="form-control" id="bankName" name="bankName">
                          <option>Bank of Kigali</option>
                          <option>other</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <input type="text" class="form-control" id="accountNumber" name="accountNumber"
                          placeholder="bank account number" required>
                      </div>
                      <div class="form-group">
                        <input type="number" class="form-control" id="amount" name="amount"
                          placeholder="amount to pay"
                          required>
                      </div>
                      <div class="form-group">
                        <textarea class="form-control" name="comment" cols="10" rows="2" placeholder=" comment" required></textarea>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <h5 class="text-center">Bank credentials</h5>
                      <hr>

                      <div class="form-group">
                        <input type="email" class="form-control" id="email" name="email"
                          placeholder="Email" required>
                      </div>

                      <div class="form-group">
                        <input type="password" class="form-control" id="password" name="password"
                          placeholder="Password" required>
                      </div>
                      <div class="form-group">
                        <input name="_method" type="hidden" value="POST">
                        <button type="submit" id="payBtn" class="btn btn-sm btn-block btn-primary show_confirm"
                          data-toggle="tooltip"
                          @empty($registration) disabled title="can't make payment without registration form" @endempty>pay
                          now</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            <div class="tab-pane container" id="menu1">
              @if (!empty($registration))
                <h5 class="mt-4 text-center">Registration Form</h5>
                <hr>
                <div style="width: 80%;margin:auto;font-size: 14px;">
                  <div class="row">
                    <div class="col-md-6 text-left">
                      <p>Student Number: {{ $student->student_id }}</p>
                      <p>Student Names: {{ $student->names }}</p>
                    </div>
                    <div class="col-md-6 text-right">
                      <p>Faculty: {{ $faculty->name }}</p>
                      <p>Department: {{ $department->name }}</p>
                      @if (!empty($registration))
                        <p>Program: {{ $registration->program }}</p>
                      @else
                        <p>Program: </p>
                      @endif
                    </div>
                  </div>
                  <hr>
                  <table class="table table-borderless text-center">
                    <thead>
                      <tr>
                        <th>CODE</th>
                        <th>COURSE DESCRIPTION</th>
                        <th>CREDITS</th>
                        <th>GROUP</th>
                        <th>COST/Cr</th>
                        <th>TOTAL</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($courses as $course)
                        <tr>
                          <td>{{ $course->code }}</td>
                          <td>{{ $course->name }}</td>
                          <td>{{ $course->credits }}</td>
                          <td>{{ $group }}</td>
                          <td>{{ number_format($creditCost, 0, null, ',') }}</td>
                          <td>{{ number_format($creditCost * $course->credits, 0, null, ',') }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                  <hr>
                  <p class="text-center">Total credits: {{ $totalCredits }}</p>
                </div>
                <div style="width: 50%;margin:auto;border:1px solid black;">
                  <div class="row">
                    <div class="col-sm-6">
                      <span class="pl-4">Registration Fee: </span>
                    </div>
                    <div class="col-sm-6">
                      <span>{{ number_format($otherFees['registrationFee'], 0, null, ',') }}</span>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <span class="pl-4">Late Fine: </span>
                    </div>
                    <div class="col-sm-6">
                      <span>{{ number_format($otherFees['lateFineFee'], 0, null, ',') }}</span>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <span class="pl-4">Facility Fee: </span>
                    </div>
                    <div class="col-sm-6">
                      <span>{{ number_format($otherFees['facilityFee'], 0, null, ',') }}</span>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <span class="pl-4">Research Manual: </span>
                    </div>
                    <div class="col-sm-6">
                      <span>{{ number_format($otherFees['researchManualFee'], 0, null, ',') }}</span>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <span class="pl-4">Student Card: </span>
                    </div>
                    <div class="col-sm-6">
                      <span>{{ number_format($otherFees['studentCardFee'], 0, null, ',') }}</span>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <span class="pl-4">Graduation Fee: </span>
                    </div>
                    <div class="col-sm-6">
                      <span>{{ number_format($otherFees['graduationFee'], 0, null, ',') }}</span>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <span class="pl-4">Library Card Fee: </span>
                    </div>
                    <div class="col-sm-6">
                      <span>{{ number_format($otherFees['libraryCardFee'], 0, null, ',') }}</span>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <span class="pl-4">Sanitation Fee: </span>
                    </div>
                    <div class="col-sm-6">
                      <span>{{ number_format($otherFees['sanitationFee'], 0, null, ',') }}</span>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <span class="pl-4">Tuition Fee: </span>
                    </div>
                    <div class="col-sm-6">
                      <span>{{ number_format($tuitionFee, 0, null, ',') }}</span>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <span class="pl-4"><b>TOTAL TO BE PAID: </b></span>
                    </div>
                    <div class="col-sm-6">
                      <span><b>{{ number_format($totalFee, 0, null, ',') }}</b></span>
                    </div>
                  </div>
                </div>
              @else
                <h5 class="mt-4 text-center">Registration Form</h5>
                <hr>
                <div style="width: 80%;margin:auto;">
                  <div class="jumbotron text-center">
                    <p>No registration found</p>
                    <h5>You have not registered any course this semester</h5>
                  </div>
                  <hr>
                </div>
              @endif
            </div>
            <hr>
            <div class="tab-pane container" id="menu2">
              {{-- Payment Summary --}}
              @if (!empty($registration))
                <div class="row">
                  <div class="col-lg-6">
                    <h1 class="h4 lead text-gray-800">Payment Summary</h1>
                    <hr>
                    <small>Student charges for semester {{ $registration->semester }}
                      {{ $registrationYear }}:&nbsp;&nbsp;
                      <b><u>{{ number_format($charge->total_charges, 0, null, ',') }}</u></b></small><br>
                    <small>Amount paid: {{ number_format($charge->amount_paid, 0, null, ',') }}</small><br>
                    <small>Amount due: {{ number_format($charge->amount_due, 0, null, ',') }}</small><br>
                    <br>
                    <hr>
                  </div>
                  <div class="col-lg-6">
                    <h1 class="h4 lead text-gray-800">Payment History</h1>
                    <hr>
                    @forelse ($payments as $payment)
                      <small>Payment date: {{ $payment->created_at }}&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</small>
                      <small>Amount paid: {{ number_format($payment->amount, 0, null, ',') }} Frw</small>
                      <br>
                      <hr>
                    @empty
                      <h5 class="jumbotron text-center">No payments found.</h5>
                    @endforelse
                  </div>
                </div>
              @else
                <div class="jumbotron text-center">
                  <h1 class="h4 lead text-gray-800">Payment History</h1>
                  <hr>
                  @forelse ($payments as $payment)
                    <small>Payment date: {{ $payment->created_at }}&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</small>
                    <small>Amount paid: {{ number_format($payment->amount, 0, null, ',') }} Frw</small>
                    <br>
                    <hr>
                  @empty
                    <p>No payments available for this account.</p>
                  @endforelse
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
      <div class="flex justify-center mt-4 sm:items-center sm:justify-between">
        <div class="text-center text-sm text-gray-500 sm:text-left">
          <div class="flex items-center">
            <span>Made with</span>
            <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              viewBox="0 0 24 24" class="ml-4 -mt-px w-5 h-5 text-gray-400">
              <path
                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
              </path>
            </svg>
            <span>&nbsp;&nbsp;&nbsp;&nbsp;By&nbsp;</span>
            <a href="https://github.com/asokaniyonsaba" class="ml-1 underline">
              Asoka Niyonsaba
            </a>
          </div>
        </div>
        <div class="ml-4 text-center text-sm text-gray-500 sm:text-right sm:ml-0">
          Auca@2022
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
