# i7, 3,4Ghz, 4,5 millions in narro\_context\_info, import ran on a project with 7000 contexts #

context, context\_info, suggestion: 8000 queries, 120s, 60s on database queries

context, no context\_info, suggestions: 50 000 queries, 644s, 578s on database queries

no context, no context\_info, no suggestions: 65 772 queries, 1341s, 1273s on database queries

There's some room for improvement. 50 000 queries for 7000 context for which the information is in 4 tables seems like a lot.