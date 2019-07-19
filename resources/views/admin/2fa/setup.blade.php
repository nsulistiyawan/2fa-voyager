<h2> Setup Google 2Fa Login </h2>

<hr>

<form action="{{ route('admin.do-setup-2fa') }}" autocomplete="off" method="post">
    @csrf

    Please scan the barcode / input 2fa secret key below.
    Then input the generated otp code on otp field.

    Google 2FA Secret
    <br>
    <h3> {{ $secret }} </h3>
    <br>

    Google 2FA Image
    <br><br>

    <img src="{{ $secretUrl }}" alt="">

    <br><br>
    OTP Code
    <input type="text" name="otp" id="otp">
    <input type="hidden" name="secret" value="{{ $secret }}">

    <br><br>

    @if($errors->has('otp'))
        {{ $errors->first('otp') }}
    @endif

    <br>
    <br>

    
    <button type="submit"> Verify OTP </button>

</form>