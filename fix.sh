chown -R :nginx ./storage
chown -R :nginx ./bootstrap/cache
chmod -R 0777 ./storage
chmod -R 0775 ./bootstrap/cache
semanage fcontext -a -t httpd_sys_rw_content_t './storage(/.*)?'
semanage fcontext -a -t httpd_sys_rw_content_t './bootstrap/cache(/.*)?'
restorecon -Rv './'
