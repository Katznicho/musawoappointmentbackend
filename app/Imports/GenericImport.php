<?php

namespace App\Imports;

use App\Models\LabService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GenericImport implements ToModel, WithHeadingRow,SkipsEmptyRows
{
    function model(array $row)
    {
         //dd($row[0]['name']);
        //check if the entire row is empty
        if($row == null){
            return;
        }



        if ($row[0] == 'name' || $row[2] == 'price' ) {
            return;
        } else {




                return new LabService(
                    [
                        'name' =>$row[0],
                        'price' =>$row[2]

                    ]
                );
            }

            //dd('here');
        }
    }

