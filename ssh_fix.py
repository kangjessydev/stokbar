import pexpect
import time

def run_ssh():
    child = pexpect.spawn('ssh -o StrictHostKeyChecking=no stokbar@202.155.157.95', encoding='utf-8', timeout=10)
    
    try:
        child.expect('assword:', timeout=5)
        child.sendline('mono pangsit')
    except pexpect.TIMEOUT:
        print("Timeout waiting for password prompt.")
        return

    time.sleep(2)
    child.sendline('cd htdocs/stokbar.lihatprojek.biz.id')
    time.sleep(1)
    
    commands = [
        "php artisan filament:assets",
        "php artisan view:clear",
        "php artisan filament:optimize:clear",
        "php artisan optimize:clear"
    ]

    for cmd in commands:
        print(f"--- Running: {cmd} ---")
        child.sendline(cmd)
        time.sleep(2)
        try:
            print(child.read_nonblocking(size=10000, timeout=1))
        except pexpect.TIMEOUT:
            pass

    child.sendline('exit')

if __name__ == '__main__':
    run_ssh()
