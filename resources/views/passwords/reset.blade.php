<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SDSSU Handbook App</title>
        <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    </head>
    <body class="font-sans leading-normal tracking-normal text-gray-900 bg-gray-100">
			<!--Container-->
			<div class="container w-full md:max-w-3xl mx-auto pt-20">
			<div class="w-full px-4 md:px-6 text-xl text-gray-900 leading-normal" style="font-family:Georgia,serif;">
				<div>
							<h1 class="font-bold font-sans break-normal text-black pt-6 pb-2 text-3xl md:text-3xl"><span style="color:#0470d9">SDSSU</span> Handbook App</h1>
							<h3 class="font-bold break-normal text-gray-900 pt-6 pb-2 text-xl md:text-2xl">Password Reset</h3>
				</div>
				@if($success)
					<p class="py-2">Your temporary password for "{{$email}}" is:</p>
					<p class="py-2 text-bold">{{$password}}</p>
					<p class="py-2">Please copy the password and login to the app. You may then reset your password on "My Account" page.</p>
				@else
					<p class="py-2">Your password recovery link has expired.</p>
				@endif
				</div>

			</div>

		</body>

</html>