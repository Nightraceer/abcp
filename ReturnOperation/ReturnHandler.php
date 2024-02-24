<?php

namespace TestTask\ReturnOperation;

interface ReturnHandler
{
    public function handle(ReturnRequest $data): ReturnResult;
}
