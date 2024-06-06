<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Text to MIDI Converter</title>
</head>
<body>
    <h2>Text to MIDI Convertgdfgdfgdfer</h2>
    <form method="post">
        <textarea name="text" rows="5" cols="50" placeholder="Enter your text here"></textarea><br><br>
        <input type="submit" value="Convert to MIDI">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve text from the form
        $text = $_POST['text'];
$text = '["C4"][8n], ' . $text;

        // Generate MIDI data
        $midiData = textToMIDI($text);

        // Write the MIDI data to a file
        file_put_contents('output.mid', $midiData);

        echo "Conversion successful!";
    }

    function textToMIDI($text) {
        // Define a mapping from pitches to MIDI note numbers across multiple octaves
        $noteMap = [
            'C1' => 12, 'C#1' => 13, 'D1' => 14, 'D#1' => 15, 'E1' => 16, 'F1' => 17, 'F#1' => 18, 'G1' => 19, 'G#1' => 20, 'A1' => 21, 'A#1' => 22, 'B1' => 23,
            'C2' => 24, 'C#2' => 25, 'D2' => 26, 'D#2' => 27, 'E2' => 28, 'F2' => 29, 'F#2' => 30, 'G2' => 31, 'G#2' => 32, 'A2' => 33, 'A#2' => 34, 'B2' => 35,
            'C3' => 36, 'C#3' => 37, 'D3' => 38, 'D#3' => 39, 'E3' => 40, 'F3' => 41, 'F#3' => 42, 'G3' => 43, 'G#3' => 44, 'A3' => 45, 'A#3' => 46, 'B3' => 47,
            'C4' => 48, 'C#4' => 49, 'D4' => 50, 'D#4' => 51, 'E4' => 52, 'F4' => 53, 'F#4' => 54, 'G4' => 55, 'G#4' => 56, 'A4' => 57, 'A#4' => 58, 'B4' => 59,
            'C5' => 60, 'C#5' => 61, 'D5' => 62, 'D#5' => 63, 'E5' => 64, 'F5' => 65, 'F#5' => 66, 'G5' => 67, 'G#5' => 68, 'A5' => 69, 'A#5' => 70, 'B5' => 71,
            'C6' => 72, 'C#6' => 73, 'D6' => 74, 'D#6' => 75, 'E6' => 76, 'F6' => 77, 'F#6' => 78, 'G6' => 79, 'G#6' => 80, 'A6' => 81, 'A#6' => 82, 'B6' => 83,
            'C7' => 84, 'C#7' => 85, 'D7' => 86, 'D#7' => 87, 'E7' => 88, 'F7' => 89, 'F#7' => 90, 'G7' => 91, 'G#7' => 92, 'A7' => 93, 'A#7' => 94, 'B7' => 95,
            'C8' => 96, 'C#8' => 97, 'D8' => 98, 'D#8' => 99, 'E8' => 100, 'F8' => 101, 'F#8' => 102, 'G8' => 103,'G#8' => 104, 'A8' => 105, 'A#8' => 106, 'B8' => 107,
                'C9' => 108, 'C#9' => 109, 'D9' => 110, 'D#9' => 111, 'E9' => 112, 'F9' => 113, 'F#9' => 114, 'G9' => 115, 'G#9' => 116, 'A9' => 117, 'A#9' => 118, 'B9' => 119,
            ];
    
            // Create MIDI file header
            $header = 'MThd' . pack('N', 6) . pack('n', 1) . pack('n', 1) . pack('n', 96);
    
            // Create track chunk
            $trackChunk = 'MTrk';
    
            // Split the input text by commas
            $parts = explode(",", $text);
    
            // Process each part to extract pitches and durations
            foreach ($parts as $part) {
                // Extract the pitch and duration from the part
                preg_match('/\[\"([A-G]#?[0-9])\"\]\[(\d+n)\]/', $part, $matches);
                if (isset($matches[1]) && isset($matches[2]) && isset($noteMap[$matches[1]])) {
                    $pitch = $matches[1];
                    $duration = $matches[2];
                    $note = $noteMap[$pitch];
                    // Calculate delta time based on duration
                    $deltaTime = calculateDeltaTime($duration);
                    // Note On event
                    $trackChunk .= pack('C', 0) . pack('C', 0x90) . pack('C', $note) . pack('C', 127);
                    // Note Off event (delayed by delta time)
                    $trackChunk .= pack('C', $deltaTime) . pack('C', 0x80) . pack('C', $note) . pack('C', 0);
                }
            }
    
            // Calculate track chunk length
            $trackLength = strlen($trackChunk) - 4;
            $trackChunk = substr_replace($trackChunk, pack('N', $trackLength), 4, 4);
    
            // Return MIDI data
            return $header . $trackChunk;
        }
    
        // Function to calculate delta time based on duration
        function calculateDeltaTime($duration) {
            // Convert duration to ticks
            switch ($duration) {
                case '1n':
                    return 192; // Whole note (1n) corresponds to 192 ticks in 4/4 time signature
                case '2n':
                    return 96;  // Half note (2n) corresponds to 96 ticks in 4/4 time signature
                case '4n':
                    return 48;  // Quarter note (4n) corresponds to 48 ticks in 4/4 time signature
                case '8n':
                    return 24;  // Eighth note (8n) corresponds to 24 ticks in 4/4 time signature
                case '16n':
                    return 12;  // Sixteenth note (16n) corresponds to 12 ticks in 4/4 time signature
                case '32n':
                    return 6;   // Thirty-second note (32n) corresponds to 6 ticks in 4/4 time signature
                case '64n':
                    return 3;   // Sixty-fourth note (64n) corresponds to 3 ticks in 4/4 time signature
                default:
                    return 48;  // Default to 8n if duration is not recognized
            }
            
        }
        ?>
    </body>
    </html>
    