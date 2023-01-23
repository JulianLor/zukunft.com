PREPARE batch_job_list_by_job_type FROM
    'SELECT calc_and_cleanup_task_id,
            request_time,
            start_time,
            end_time,
            calc_and_cleanup_task_type_id,
            row_id,
            change_field_id
       FROM calc_and_cleanup_tasks
      WHERE calc_and_cleanup_task_type_id = ?
      LIMIT 20';