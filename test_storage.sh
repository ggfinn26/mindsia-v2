#!/bin/bash

BOT_TOKEN=$(grep TELEGRAM_BOT_TOKEN mindsia-v2/.env | cut -d= -f2)
GROUP_STORAGE=$(grep GROUP_STORAGE mindsia-v2/.env | cut -d= -f2 | tr -d '"')
GROUP_LOG=$(grep GROUP_LOG mindsia-v2/.env | cut -d= -f2 | tr -d '"')
API="https://api.telegram.org/bot${BOT_TOKEN}"

echo "=== Telegram Storage Test ==="
echo "Polling with 30 sec timeout..."

UPDATES=$(curl -s "${API}/getUpdates?limit=100&timeout=30")

# Find photo from storage group
PHOTO_MSG=$(echo "$UPDATES" | jq ".result[] | select(.message.chat.id == $GROUP_STORAGE and .message.photo) | .message" | jq -s ".[0]")

if [ "$PHOTO_MSG" = "null" ] || [ -z "$PHOTO_MSG" ]; then
    echo "❌ No photo found in storage group"
    exit 1
fi

MSG_ID=$(echo "$PHOTO_MSG" | jq -r '.message_id')
echo "✅ Found photo in message $MSG_ID"

# Extract photo
PHOTO=$(echo "$PHOTO_MSG" | jq '.photo[-1]')
FILE_ID=$(echo "$PHOTO" | jq -r '.file_id')
FILE_SIZE=$(echo "$PHOTO" | jq -r '.file_size')

echo "✅ File ID: $FILE_ID"
echo "✅ File size: $FILE_SIZE bytes"

# Get file path
echo "Getting file path..."
FILE_INFO=$(curl -s "${API}/getFile?file_id=${FILE_ID}" | jq '.result')
FILE_PATH=$(echo "$FILE_INFO" | jq -r '.file_path')

echo "✅ File path: $FILE_PATH"

# Send log
echo "Sending log to log group..."

LOG_TEXT="[$(date '+%Y-%m-%d %H:%M:%S')] [INFO] [storage_test] Photo retrieved from storage
File ID: $FILE_ID
File size: $FILE_SIZE bytes
File path: $FILE_PATH"

curl -s "${API}/sendMessage" \
    --data-urlencode "chat_id=$GROUP_LOG" \
    --data-urlencode "text=$LOG_TEXT" > /dev/null

echo "✅ Log sent to group"
echo ""
echo "=== Test Complete ✅ ==="
