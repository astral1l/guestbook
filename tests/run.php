<?php

require dirname(__DIR__) . '/src/helpers.php';
require dirname(__DIR__) . '/src/messages.php';

$passed = 0;
$failed = 0;

function test(string $name, callable $callback): void
{
    global $passed, $failed;

    try {
        $callback();
        $passed++;
        echo "ok - {$name}\n";
    } catch (Throwable $exception) {
        $failed++;
        echo "not ok - {$name}: {$exception->getMessage()}\n";
    }
}

function assert_same($expected, $actual): void
{
    if ($expected !== $actual) {
        throw new RuntimeException('expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
}

test('valid message has no errors', function () {
    [$data, $errors] = validate_message([
        'name' => 'John',
        'email' => 'john@example.com',
        'body' => 'Hello there',
    ]);

    assert_same([], $errors);
    assert_same('John', $data['name']);
});

test('name is required', function () {
    [, $errors] = validate_message(['name' => ' ', 'body' => 'Hello']);
    assert_same('Enter your name.', $errors['name'] ?? null);
});

test('name must be 2 to 50 characters', function () {
    [, $errors] = validate_message(['name' => 'J', 'body' => 'Hello']);
    assert_same('Name must be 2 to 50 characters.', $errors['name'] ?? null);

    [, $errors] = validate_message(['name' => str_repeat('a', 51), 'body' => 'Hello']);
    assert_same('Name must be 2 to 50 characters.', $errors['name'] ?? null);
});

test('name length counts characters, not bytes', function () {
    [, $errors] = validate_message(['name' => str_repeat('ф', 50), 'body' => 'Hello']);
    assert_same([], $errors);

    [, $errors] = validate_message(['name' => str_repeat('ф', 51), 'body' => 'Hello']);
    assert_same('Name must be 2 to 50 characters.', $errors['name'] ?? null);
});

test('email is optional but must be valid', function () {
    [, $errors] = validate_message(['name' => 'John', 'body' => 'Hello']);
    assert_same([], $errors);

    [, $errors] = validate_message(['name' => 'John', 'email' => 'not-an-email', 'body' => 'Hello']);
    assert_same('Enter a valid email address.', $errors['email'] ?? null);
});

test('email is trimmed', function () {
    [$data, $errors] = validate_message(['name' => 'John', 'email' => ' john@example.com ', 'body' => 'Hello']);
    assert_same([], $errors);
    assert_same('john@example.com', $data['email']);
});

test('message must be 3 to 1000 characters', function () {
    [, $errors] = validate_message(['name' => 'John', 'body' => 'Hi']);
    assert_same('Message must be 3 to 1000 characters.', $errors['body'] ?? null);

    [, $errors] = validate_message(['name' => 'John', 'body' => str_repeat('a', 1001)]);
    assert_same('Message must be 3 to 1000 characters.', $errors['body'] ?? null);
});

test('page number falls back to 1', function () {
    assert_same(1, page_number('0'));
    assert_same(1, page_number('-3'));
    assert_same(1, page_number('abc'));
    assert_same(4, page_number('4'));
});

test('page count and offset', function () {
    assert_same(1, page_count(0));
    assert_same(1, page_count(10));
    assert_same(2, page_count(11));
    assert_same(0, page_offset(1));
    assert_same(10, page_offset(2));
});

test('html is escaped', function () {
    assert_same('&lt;script&gt;alert(1)&lt;/script&gt;', e('<script>alert(1)</script>'));
});

echo PHP_EOL . "{$passed} passed, {$failed} failed" . PHP_EOL;

exit($failed === 0 ? 0 : 1);
