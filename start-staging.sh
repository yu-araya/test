# aws cliが入っていること前提

aws autoscaling update-auto-scaling-group \
  --auto-scaling-group-name tpay2-stg-as-group \
  --min-size 2 \
  --max-size 2 \
  --desired-capacity 2

aws rds start-db-instance \
  --db-instance-identifier tpay2-stg-rds
