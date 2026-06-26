# Test Data Seeding Script for Support Desk (PowerShell)

Write-Host "`n╔════════════════════════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║  Support Desk - Test Data Seeding Script                       ║" -ForegroundColor Green
Write-Host "╚════════════════════════════════════════════════════════════════╝`n" -ForegroundColor Green

Set-Location "d:\Richard.WWW\support_desk-main"

Write-Host "📊 This will seed the database with:" -ForegroundColor Cyan
Write-Host "  • 8 Departments" -ForegroundColor White
Write-Host "  • 1200+ Users (all with password: 123)" -ForegroundColor White
Write-Host "  • 20+ Clients" -ForegroundColor White
Write-Host "  • 9000+ Tickets (6 months of data)" -ForegroundColor White
Write-Host "  • 30000+ Comments" -ForegroundColor White
Write-Host "  • Attachments, History, Leave Requests, Audit Trails" -ForegroundColor White

Write-Host "`n⏱️  Estimated time: 2-5 minutes`n" -ForegroundColor Yellow

Write-Host "🔄 Wiping existing data..." -ForegroundColor Cyan
php artisan db:wipe --no-interaction 2>$null

Write-Host "🔄 Running migrations..." -ForegroundColor Cyan
php artisan migrate

Write-Host "`n🌱 Seeding database..." -ForegroundColor Cyan
php artisan db:seed

Write-Host "`n✅ Seeding completed!`n" -ForegroundColor Green

Write-Host "🔑 Test Credentials:" -ForegroundColor Yellow
Write-Host "  Username: operator_0001 (or any operator_XXXX)" -ForegroundColor White
Write-Host "  Password: 123" -ForegroundColor White

Write-Host "`n🌐 Other Test Users:" -ForegroundColor Yellow
Write-Host "  system_admin / 123" -ForegroundColor White
Write-Host "  super_user / 123" -ForegroundColor White
Write-Host "  support_manager / 123" -ForegroundColor White

Write-Host "`n🌐 Access the application:" -ForegroundColor Cyan
Write-Host "  http://localhost:8000 or http://127.0.0.1:8000" -ForegroundColor White

Write-Host "`n✨ Enjoy your fully populated test system!`n" -ForegroundColor Green
