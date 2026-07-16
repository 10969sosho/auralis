<x-emails::layout>
    <p>Hello <strong>{{ $user->name }}</strong>,</p>
    <p>Your account with Auralis8 has been successfully created.</p>

    <div class="email-details">
        <table>
            <tr><td>Email</td><td>{{ $user->email }}</td></tr>
            <tr><td>Account Type</td><td>{{ ucfirst($user->account_type ?? 'Regular') }}</td></tr>
        </table>
    </div>

    @if($password)
    <p>Your temporary password is: <strong>{{ $password }}</strong></p>
    <p>Please login and change your password as soon as possible.</p>
    @endif

    <p>Thank you for choosing Auralis8!</p>
</x-emails::layout>
