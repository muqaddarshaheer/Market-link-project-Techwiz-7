<?php

namespace App\Http\Controllers;

class ProduceGuideController extends Controller
{
    public function index()
    {
        $items = config('produce_guide.items', []);

        $fruits = [];
        $vegetables = [];
        foreach ($items as $key => $item) {
            $row = array_merge(['key' => $key], $item);
            if (($item['type'] ?? '') === 'fruit') {
                $fruits[$key] = $row;
            } else {
                $vegetables[$key] = $row;
            }
        }

        return view('produce-guide.index', compact('fruits', 'vegetables', 'items'));
    }
}
