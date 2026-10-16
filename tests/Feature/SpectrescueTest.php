<?php

use App\Models\User;
use App\Models\Incident;
use App\Models\IncidentType;
use App\Models\Responder;
use App\Models\Assignment;
use App\Models\Evidence;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('landing page loads successfully', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('SPECT');
    $response->assertSee('RESCUE');
});

test('login page loads successfully', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
    $response->assertSee('SPECTRESCUE');
});

test('citizen can authenticate and view citizen hub', function () {
    $citizen = User::where('role', 'citizen')->first();
    $response = $this->actingAs($citizen)->get('/citizen/dashboard');
    $response->assertStatus(200);
    $response->assertSee('Citizen Command Hub');
});

test('dispatcher can view dispatch console', function () {
    $dispatcher = User::where('role', 'dispatcher')->first();
    $response = $this->actingAs($dispatcher)->get('/dispatcher/dashboard');
    $response->assertStatus(200);
    $response->assertSee('Dispatcher Control Console');
});

test('responder can view missions dashboard', function () {
    $responderUser = User::where('role', 'responder')->whereHas('responderProfile', fn($q) => $q->where('verification_status', 'approved'))->first();
    $response = $this->actingAs($responderUser)->get('/responder/dashboard');
    $response->assertStatus(200);
    $response->assertSee('Field Responder Operations');
});

test('admin can view command control center', function () {
    $admin = User::where('role', 'admin')->first();
    $response = $this->actingAs($admin)->get('/admin/dashboard');
    $response->assertStatus(200);
    $response->assertSee('Headquarters Control Center');
});

test('complete emergency dispatch and resolution cycle', function () {
    $citizen = User::where('role', 'citizen')->first();
    $dispatcher = User::where('role', 'dispatcher')->first();
    $responder = Responder::where('verification_status', 'approved')
        ->whereDoesntHave('assignments', fn($q) => $q->whereIn('status', ['assigned', 'accepted'])->whereHas('incident', fn($i) => $i->whereIn('status', ['Dispatched', 'Responding'])))
        ->first();
    $incidentType = IncidentType::first();

    // 1. Citizen reports emergency with GPS coordinates
    $this->actingAs($citizen)->post('/citizen/incidents', [
        'incident_type_id' => $incidentType->id,
        'title'            => 'Levitating Objects in Science Lab',
        'description'      => 'Beakers and chairs floating mid-air, temperature dropped suddenly.',
        'location_address' => '404 Science Wing, UP Diliman',
        'location_city'    => 'Quezon City',
        'latitude'         => 14.6538000,
        'longitude'        => 121.0685000,
        'threat_level'     => 'High',
    ])->assertRedirect();

    $incident = Incident::where('title', 'Levitating Objects in Science Lab')->first();
    expect($incident)->not->toBeNull();
    expect($incident->status)->toBe('Pending');
    expect($incident->hasCoordinates())->toBeTrue();
    expect((float)$incident->latitude)->toBe(14.6538);
    expect((float)$incident->longitude)->toBe(121.0685);

    // Citizen can view incident with map display
    $citizenShow = $this->actingAs($citizen)->get("/citizen/incidents/{$incident->id}");
    $citizenShow->assertStatus(200);
    $citizenShow->assertSee('incident-display-map');

    // 2. Dispatcher assigns responder
    $this->actingAs($dispatcher)->post("/dispatcher/incidents/{$incident->id}/assign", [
        'responder_id'     => $responder->id,
        'dispatcher_notes' => 'Deploy high-grade containment traps.',
        'threat_level'     => 'High',
    ])->assertRedirect();

    $incident->refresh();
    expect($incident->status)->toBe('Dispatched');

    // 3. Responder accepts mission
    $assignment = Assignment::where('incident_id', $incident->id)->first();
    $this->actingAs($responder->user)->post("/responder/assignments/{$assignment->id}/respond", [
        'action' => 'accept',
        'responder_notes' => 'On site, preparing containment.',
    ])->assertRedirect();

    $incident->refresh();
    expect($incident->status)->toBe('Responding');

    // 4. Responder submits report and marks incident as Resolved
    $this->actingAs($responder->user)->post("/responder/incidents/{$incident->id}/report", [
        'containment_method' => 'Salt Ring Boundary & Iron filings Array',
        'findings_summary'   => 'Class III Poltergeist bound to brass pendulum. Entity dispelled using salt line.',
        'threat_neutralized' => 1,
        'evidence_collected' => 'EMF anomaly reading of 52 mG',
        'recommendations'    => 'Premises cleared.',
    ])->assertRedirect();

    $incident->refresh();
    expect($incident->status)->toBe('Resolved');
    expect($incident->responseReport)->not->toBeNull();
});

test('citizen can access incidents index and edit report to update ground zero map coordinates', function () {
    $citizen = User::where('role', 'citizen')->first();
    $incidentType = IncidentType::first();

    // 1. Citizen can view /citizen/incidents
    $indexResponse = $this->actingAs($citizen)->get('/citizen/incidents');
    $indexResponse->assertStatus(200);
    $indexResponse->assertSee('My Paranormal Reports');

    // 2. Create an incident without GPS coordinates (simulating user forgetting pin)
    $incident = Incident::create([
        'citizen_id'       => $citizen->id,
        'incident_type_id' => $incidentType->id,
        'tracking_code'    => 'PAR-TEST-0099',
        'title'            => 'Cryptid Howling in Backyard',
        'description'      => 'Strange glowing eyes and deep growling near trees.',
        'location_address' => '12 Old Pine Road',
        'location_city'    => 'Baguio City',
        'latitude'         => null,
        'longitude'        => null,
        'threat_level'     => 'Medium',
        'status'           => 'Pending',
    ]);

    expect($incident->hasCoordinates())->toBeFalse();

    // 3. Citizen visits show page - sees prompt to drop map pin
    $showResponse = $this->actingAs($citizen)->get("/citizen/incidents/{$incident->id}");
    $showResponse->assertStatus(200);
    $showResponse->assertSee('No Tactical Map Pin Dropped');
    $showResponse->assertSee('Edit Report &amp; Map Pin', false);

    // 4. Citizen visits edit page
    $editResponse = $this->actingAs($citizen)->get("/citizen/incidents/{$incident->id}/edit");
    $editResponse->assertStatus(200);
    $editResponse->assertSee('Cryptid Howling in Backyard');

    // 5. Citizen updates incident with new map coordinates and revised details
    $updateResponse = $this->actingAs($citizen)->put("/citizen/incidents/{$incident->id}", [
        'incident_type_id' => $incidentType->id,
        'title'            => 'Cryptid Howling in Backyard - Pin Updated',
        'description'      => 'Identified as large canine apparition.',
        'location_address' => '12 Old Pine Road',
        'location_city'    => 'Baguio City',
        'latitude'         => 16.4023000,
        'longitude'        => 120.5960000,
        'threat_level'     => 'High',
    ]);

    $updateResponse->assertRedirect("/citizen/incidents/{$incident->id}");

    $incident->refresh();
    expect($incident->title)->toBe('Cryptid Howling in Backyard - Pin Updated');
    expect($incident->hasCoordinates())->toBeTrue();
    expect((float)$incident->latitude)->toBe(16.4023);
    expect((float)$incident->longitude)->toBe(120.596);
    expect($incident->threat_level)->toBe('High');
});

test('citizen can remove uploaded evidence image with delete action in edit view', function () {
    $citizen = User::where('role', 'citizen')->first();
    $incidentType = IncidentType::first();

    $incident = Incident::create([
        'citizen_id'       => $citizen->id,
        'incident_type_id' => $incidentType->id,
        'tracking_code'    => 'PAR-TEST-0088',
        'title'            => 'Floating Books in Study Room',
        'description'      => 'Pages tearing themselves out.',
        'location_address' => '7 University Avenue',
        'location_city'    => 'Quezon City',
        'latitude'         => 14.6538,
        'longitude'        => 121.0685,
        'threat_level'     => 'Low',
        'status'           => 'Pending',
    ]);

    $evidence = Evidence::create([
        'incident_id' => $incident->id,
        'file_path'   => 'evidence/sample_anomaly.jpg',
        'file_name'   => 'sample_anomaly.jpg',
        'file_type'   => 'image',
        'caption'     => 'Floating encyclopedia photo',
    ]);

    expect($incident->evidence()->count())->toBe(1);

    // Edit page shows the evidence and delete button
    $editPage = $this->actingAs($citizen)->get("/citizen/incidents/{$incident->id}/edit");
    $editPage->assertStatus(200);
    $editPage->assertSee('sample_anomaly.jpg');
    $editPage->assertSee("delete-evidence-{$evidence->id}");

    // Citizen deletes the evidence
    $deleteResponse = $this->actingAs($citizen)->delete("/citizen/incidents/{$incident->id}/evidence/{$evidence->id}");
    $deleteResponse->assertRedirect();
    $deleteResponse->assertSessionHas('success', 'Evidence image removed successfully.');

    expect(Evidence::find($evidence->id))->toBeNull();
    expect($incident->evidence()->count())->toBe(0);
});

test('dispatcher cannot deploy the same responder multiple times while deployed', function () {
    $dispatcher = User::where('role', 'dispatcher')->first();
    $responder = Responder::with('user')->where('verification_status', 'approved')
        ->whereDoesntHave('assignments', fn($q) => $q->whereIn('status', ['assigned', 'accepted'])->whereHas('incident', fn($i) => $i->whereIn('status', ['Dispatched', 'Responding'])))
        ->first();
    $citizen = User::where('role', 'citizen')->first();
    $incidentType = IncidentType::first();

    // Create 2 pending incidents
    $incidentA = Incident::create([
        'citizen_id'       => $citizen->id,
        'incident_type_id' => $incidentType->id,
        'tracking_code'    => 'PAR-TEST-A001',
        'title'            => 'Emergency Alpha',
        'description'      => 'Ghost in the kitchen',
        'location_address' => '101 Spectral Blvd',
        'location_city'    => 'Manila',
        'threat_level'     => 'Medium',
        'status'           => 'Pending',
    ]);

    $incidentB = Incident::create([
        'citizen_id'       => $citizen->id,
        'incident_type_id' => $incidentType->id,
        'tracking_code'    => 'PAR-TEST-B002',
        'title'            => 'Emergency Beta',
        'description'      => 'Poltergeist in attic',
        'location_address' => '202 Ecto Street',
        'location_city'    => 'Manila',
        'threat_level'     => 'High',
        'status'           => 'Pending',
    ]);

    // 1. Dispatcher deploys responder to Incident A -> Should succeed
    $this->actingAs($dispatcher)->post("/dispatcher/incidents/{$incidentA->id}/assign", [
        'responder_id'     => $responder->id,
        'dispatcher_notes' => 'Deploying specialist to Alpha.',
    ])->assertRedirect();

    $incidentA->refresh();
    expect($incidentA->status)->toBe('Dispatched');

    // 2. Dispatcher tries to deploy the SAME responder to Incident B while still deployed to A -> Should fail
    $failResponse = $this->actingAs($dispatcher)->post("/dispatcher/incidents/{$incidentB->id}/assign", [
        'responder_id'     => $responder->id,
        'dispatcher_notes' => 'Deploying same specialist again.',
    ]);
    $failResponse->assertSessionHas('error');

    $incidentB->refresh();
    expect($incidentB->status)->toBe('Pending'); // Did not dispatch

    // 3. Dispatcher tries to deploy another responder to Incident A which already has an active specialist -> Should fail
    $failDuplicate = $this->actingAs($dispatcher)->post("/dispatcher/incidents/{$incidentA->id}/assign", [
        'responder_id'     => $responder->id,
        'dispatcher_notes' => 'Deploying again to same incident.',
    ]);
    $failDuplicate->assertSessionHas('error');

    // 4. Incident A show page should display active mission card instead of deploy button
    $showPage = $this->actingAs($dispatcher)->get("/dispatcher/incidents/{$incidentA->id}");
    $showPage->assertStatus(200);
    $showPage->assertSee('Specialist Deployed');
    $showPage->assertSee('MISSION DEPLOYMENT LOCK');
    $showPage->assertDontSee('Deploy Specialist Now');
});

test('admin can view and edit user accounts', function () {
    $admin = User::where('role', 'admin')->first();
    $citizen = User::where('role', 'citizen')->first();

    // 1. Admin can access user index and see View and Edit links
    $indexResponse = $this->actingAs($admin)->get('/admin/users');
    $indexResponse->assertStatus(200);
    $indexResponse->assertSee("admin/users/{$citizen->id}", false);
    $indexResponse->assertSee("admin/users/{$citizen->id}/edit", false);

    // 2. Admin can view the user dossier page
    $showResponse = $this->actingAs($admin)->get("/admin/users/{$citizen->id}");
    $showResponse->assertStatus(200);
    $showResponse->assertSee($citizen->name);
    $showResponse->assertSee($citizen->email);
    $showResponse->assertSee('Account Specifications');

    // 3. Admin can access the edit user page
    $editResponse = $this->actingAs($admin)->get("/admin/users/{$citizen->id}/edit");
    $editResponse->assertStatus(200);
    $editResponse->assertSee('Edit User Profile');
    $editResponse->assertSee($citizen->name);

    // 4. Admin updates the user details
    $updateResponse = $this->actingAs($admin)->put("/admin/users/{$citizen->id}", [
        'name'      => 'Updated Citizen Name',
        'email'     => $citizen->email,
        'phone'     => '0917 123 4567',
        'role'      => 'citizen',
        'is_active' => 1,
    ]);
    $updateResponse->assertRedirect("/admin/users/{$citizen->id}");
    $updateResponse->assertSessionHas('success');

    $citizen->refresh();
    expect($citizen->name)->toBe('Updated Citizen Name');
    expect($citizen->phone)->toBe('0917 123 4567');

    // 5. Non-admin users cannot access admin user management
    $this->actingAs($citizen)->get('/admin/users')->assertStatus(403);
    $this->actingAs($citizen)->get("/admin/users/{$citizen->id}")->assertStatus(403);
    $this->actingAs($citizen)->get("/admin/users/{$citizen->id}/edit")->assertStatus(403);
});

test('admin can access create user form and create citizen or responder', function () {
    $admin = User::where('role', 'admin')->first();

    // 1. View create form
    $createResponse = $this->actingAs($admin)->get('/admin/users/create');
    $createResponse->assertStatus(200);
    $createResponse->assertSee('Add New User Account');

    // 2. Create a new citizen account
    $storeCitizenResponse = $this->actingAs($admin)->post('/admin/users', [
        'name'      => 'Test Registered Citizen',
        'email'     => 'newcitizen@spectrescue.org',
        'phone'     => '0912 345 6789',
        'role'      => 'citizen',
        'password'  => 'password123',
        'is_active' => 1,
    ]);
    $newCitizen = User::where('email', 'newcitizen@spectrescue.org')->first();
    expect($newCitizen)->not->toBeNull();
    $storeCitizenResponse->assertRedirect("/admin/users/{$newCitizen->id}");

    // 3. Create a new specialist responder account with profile
    $storeResponderResponse = $this->actingAs($admin)->post('/admin/users', [
        'name'                => 'Father Gabriel Byrne',
        'email'               => 'father.gabriel@spectrescue.org',
        'phone'               => '0918 888 7777',
        'role'                => 'responder',
        'password'            => 'exorcist123',
        'is_active'           => 1,
        'specialization'      => 'Demonologist & Exorcist',
        'badge_number'        => 'RES-2026-9999',
        'bio'                 => 'Vatican certified occult specialist.',
        'verification_status' => 'approved',
        'is_available'        => 1,
    ]);
    $newResponderUser = User::where('email', 'father.gabriel@spectrescue.org')->first();
    expect($newResponderUser)->not->toBeNull();
    expect($newResponderUser->responderProfile)->not->toBeNull();
    expect($newResponderUser->responderProfile->specialization)->toBe('Demonologist & Exorcist');
    expect($newResponderUser->responderProfile->badge_number)->toBe('RES-2026-9999');
    $storeResponderResponse->assertRedirect("/admin/users/{$newResponderUser->id}");
});

test('admin side flowchart core endpoints are all accessible and functional', function () {
    $admin = User::where('role', 'admin')->first();

    // 1. Admin Dashboard (Overview and System Statistics)
    $this->actingAs($admin)->get('/admin/dashboard')
        ->assertStatus(200)
        ->assertSee('Headquarters Control Center')
        ->assertSee('Admin Management Modules');

    // 2. Manage Users (View, Add, Edit, Deactivate)
    $this->actingAs($admin)->get('/admin/users')->assertStatus(200)->assertSee('Manage Users');
    $this->actingAs($admin)->get('/admin/users/create')->assertStatus(200);

    // 3. Verify Responders (Review Responder Applications)
    $this->actingAs($admin)->get('/admin/responders')->assertStatus(200)->assertSee('Field Responders Directory');

    // 4. Manage Incidents (View, Update, Monitor Incidents)
    $this->actingAs($admin)->get('/admin/incidents')->assertStatus(200)->assertSee('Master Incident Registry');

    // 5. System Reports (View Reports and Analytics)
    $this->actingAs($admin)->get('/admin/reports')->assertStatus(200)->assertSee('Paranormal Resolution Dossiers');

    // 6. In-App Notifications (Send announcements or notifications)
    $this->actingAs($admin)->get('/admin/notifications')->assertStatus(200)->assertSee('Send In-App Notifications');

    // 7. System Settings (Manage system information)
    $this->actingAs($admin)->get('/admin/settings')->assertStatus(200)->assertSee('System Settings');

    // 8. Profile Management
    $this->actingAs($admin)->get('/profile')->assertStatus(200)->assertSee('Profile Settings');

    // 9. Phenomenon Classifications (Incident Types CRUD)
    $this->actingAs($admin)->get('/admin/incident-types')->assertStatus(200)->assertSee('Phenomenon Classifications');
    $this->actingAs($admin)->get('/admin/incident-types/create')->assertStatus(200)->assertSee('Add Phenomenon Classification');
});

test('admin can create, update, and delete phenomenon classifications', function () {
    $admin = User::where('role', 'admin')->first();

    // 1. Create a new classification
    $response = $this->actingAs($admin)->post('/admin/incident-types', [
        'name' => 'Spectral Doppelganger',
        'default_threat_level' => 'High',
        'description' => 'Entities that take the form of residents.',
    ]);
    $response->assertRedirect('/admin/incident-types');
    $this->assertDatabaseHas('incident_types', ['name' => 'Spectral Doppelganger']);

    $type = \App\Models\IncidentType::where('name', 'Spectral Doppelganger')->first();

    // 2. Edit the classification
    $this->actingAs($admin)->get("/admin/incident-types/{$type->id}/edit")
        ->assertStatus(200)
        ->assertSee('Spectral Doppelganger');

    $updateResponse = $this->actingAs($admin)->put("/admin/incident-types/{$type->id}", [
        'name' => 'Spectral Doppelganger Updated',
        'default_threat_level' => 'Critical',
        'description' => 'Updated description of mimic entity.',
    ]);
    $updateResponse->assertRedirect('/admin/incident-types');
    $this->assertDatabaseHas('incident_types', ['name' => 'Spectral Doppelganger Updated', 'default_threat_level' => 'Critical']);

    // 3. Delete classification (no incidents assigned)
    $deleteResponse = $this->actingAs($admin)->delete("/admin/incident-types/{$type->id}");
    $deleteResponse->assertRedirect();
    $this->assertDatabaseMissing('incident_types', ['id' => $type->id]);
});



