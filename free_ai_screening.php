<?php

function freeAIScreening($resume_text, $job_description){

    // Convert to lowercase
    $resume = strtolower($resume_text);
    $job = strtolower($job_description);

    // Remove special characters
    $resume = preg_replace("/[^a-z0-9 ]/", " ", $resume);
    $job = preg_replace("/[^a-z0-9 ]/", " ", $job);

    // Convert to words
    $resume_words = array_unique(explode(" ", $resume));
    $job_words = array_unique(explode(" ", $job));

    // Remove empty words
    $resume_words = array_filter($resume_words);
    $job_words = array_filter($job_words);

    // Find matched words
    $matched = array_intersect($resume_words, $job_words);

    // Score calculation
    if(count($job_words) > 0){
        $score = (count($matched) / count($job_words)) * 100;
    } else {
        $score = 0;
    }

    $score = round($score);

    // Decision logic
    if($score >= 70){
        $status = "Shortlisted";
    } elseif($score >= 40){
        $status = "Review";
    } else {
        $status = "Rejected";
    }

    // Top matched skills (first 10 words)
    $skills = array_slice($matched, 0, 10);

    // Remark
    $remark = "Matched Skills: " . implode(", ", $skills);

    return [
        "score" => $score,
        "status" => $status,
        "remark" => $remark
    ];
}
?>