$pending = FacadesDB::table('requests')->where([['client_id', '=', $id], ['status', '=', 'pending']])->get();
        if ($pending->isEmpty()) {
            $lat1 = $client->latitude;
            $long1 = $client->longitude;
            $health_worker = $client->health_worker;
            $fname = $client->fname;
            $lname = $client->lname;
            $address = $client->address;
            $names = $fname . ' ' . $lname;
            $role = FacadesDB::table('doctors')->where([
                ['role', '=', $health_worker],
                ['status', '=', 'active']

            ])
                ->get();

            if ($role->isEmpty()) {
                $defaultDoctor = FacadesDB::table('doctors')->where('role', '=', 'Default')->get();
                $ddoctor_id = $defaultDoctor[0]->id;
                $user_id = $defaultDoctor[0]->user_id;
                $ddoctor_name = $defaultDoctor[0]->name;
                $ddoctor_email = $defaultDoctor[0]->email;
                $insertRequest = FacadesDB::insert(
                    "
                insert into requests (client_id, doctor_id, message, request_type) values
                ('$id', '$ddoctor_id', 'Doctor: $ddoctor_name, Client: $fname $lname Location: $address', $health_worker))"
                );
                $data = [
                    'otp' => "Hello you have a new Request"
                ];
                Mail::to($ddoctor_email)
                    ->cc('adfamedicare69@gmail.com')
                    ->send(new DoctorTemplate($ddoctor_name, $names));

                //get the user token
                $user = User::find($user_id);
                $message = "You have a new patient  request from  $names . Please check your app for more details";
                $token = $user->push_token;
                if ($token) {
                    $this->sendPushNotification(
                        $token,
                        'New Patient Request',
                        $message,
                        ['data' => 'You have a new request']
                    );
                }


                $getRequest = FacadesDB::table('requests')->where('client_id', '=', $id)->orderBy("id", 'desc')->get();
                return response(['response' => 'success', 'data' => ['doctor' => $defaultDoctor[0], 'request' => $getRequest[0]]]);
            }

            $doctor = Doctor::selectRaw("*,( 6371 * acos( cos( radians(?) ) *cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?)) + sin( radians(?) ) *sin( radians( latitude ) ) )) AS distance", [$lat1, $long1, $lat1])->where([['role', '=', $health_worker], ['status', '=', 'active']])->orderBy("distance", 'asc')->get();
            $name = $doctor[0]->name;
            $doctor_id = $doctor[0]->id;
            $user_id = $doctor[0]->user_id;
            //get the user token
            $user = User::find($user_id);
            $message = "You have a new patient  request from  $names . Please check your app for more details";
            $token = $user->push_token;
            if ($token) {
                $this->sendPushNotification(
                    $token,
                    'New Patient Request',
                    $message,
                    ['data' => 'You have a new request']
                );
            }

            $request_data = FacadesDB::insert("insert into requests (client_id, doctor_id, message request_type) values ('$id', '$doctor_id', 'Doctor: $name, Client: $fname $lname Location: $address', $health_worker)");
            //send email to the doctor
            $data = [
                'otp' => "Hello you have a new Request"
            ];
            $email = $doctor[0]->email;

            Mail::to($email)
                ->cc('adfamedicare69@gmail.com')
                ->send(new DoctorTemplate($name, $names));

            // update the doctor status
            $update_doctor = FacadesDB::table('doctors')->where('id', '=', $doctor_id)->update([
                'status' => 'inactive',
            ]);

            //get the request
            $request = FacadesDB::table('requests')->where('client_id', '=', $id)->orderBy("id", 'desc')->get();

            //log activity
            $this->createActivityLog('Client', 'Client Makes a request');
            return response(['response' => 'success', 'data' => ['doctor' => $doctor[0], 'request' => $request[0]]]);
        } else {
            $d_id = $pending[0]->doctor_id;
            $pendingDoctor = FacadesDB::table('doctors')->where('id', '=', $d_id)->get();
            return response(['response' => 'success', 'data' => ['doctor' => $pendingDoctor[0], 'request' => $pending[0]]]);
        }
