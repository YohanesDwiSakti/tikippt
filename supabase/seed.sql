insert into public.packages (receipt, destination, status, latest_location)
values
  ('TKI-DEN-260607101500', 'Gianyar', 'Dalam Perjalanan', 'Keluar Hub Denpasar'),
  ('TKI-DEN-260607132000', 'Sanur', 'Sampai Tujuan', 'Alamat penerima, Sanur'),
  ('TKI-DEN-260607150500', 'Ubud', 'Terdaftar', 'Hub Denpasar')
on conflict (receipt) do update
set
  destination = excluded.destination,
  status = excluded.status,
  latest_location = excluded.latest_location,
  updated_at = now();

insert into public.package_events (package_id, status, location, note)
select id, 'Terdaftar', 'Hub Denpasar', 'Paket terdaftar'
from public.packages
where receipt in ('TKI-DEN-260607101500', 'TKI-DEN-260607132000', 'TKI-DEN-260607150500')
on conflict do nothing;

insert into public.package_events (package_id, status, location, note)
select id, status, latest_location, 'Status sample'
from public.packages
where receipt in ('TKI-DEN-260607101500', 'TKI-DEN-260607132000')
on conflict do nothing;
