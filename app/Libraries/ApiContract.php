<?php

namespace App\Libraries;

interface ApiContract
{
    public function todoSuggestionsFor(string $description): string;

}