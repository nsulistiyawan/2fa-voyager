<h2> Verify Google 2Fa Login </h2>

<hr>

<form action="{{ route('admin.do-verify-2fa') }}" autocomplete="off" method="post">
    @csrf
    <br><br>
    Please input your OTP Code
    <br><br>

    <input type="text" name="otp" id="otp">

    <br><br>


    @if($errors->has('otp'))


        {{ $errors->first('otp') }}

    @endif

    <br>
    <br>


    <button type="submit"> Verify OTP </button>

</form>