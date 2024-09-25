<?php /** @noinspection ALL */
// Unit test for sanitizeInput function
function testSanitizeInput() {
    $input = '<script>alert("Hello")</script>';
    $expectedOutput = '&lt;script&gt;alert(&quot;Hello&quot;)&lt;/script&gt;';
    $result = sanitizeInput($input);
    assert($result === $expectedOutput, 'sanitizeInput function does not correctly sanitize input');
}

// Run the unit test
testSanitizeInput();