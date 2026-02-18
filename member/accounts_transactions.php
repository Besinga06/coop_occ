<?php
require('includes/db.php');

$member_id = $_SESSION['member_id'];

$query = "

SELECT 
'Capital Share' as transaction_type,
amount,
reference_no,
status,
date_created
FROM capital_share
WHERE member_id = $member_id
UNION ALL
SELECT 
'Savings Deposit' as transaction_type,
amount,
reference_no,
status,
date_created
FROM savings
WHERE member_id = $member_id
UNION ALL
SELECT 
'Withdrawal Request' as transaction_type,
amount,
reference_no,
status,
date_created
FROM withdrawals
WHERE member_id = $member_id
ORDER BY date_created DESC

";

$result = $db->query($query);

?>
