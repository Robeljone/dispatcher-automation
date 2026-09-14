<?php

namespace App\Enums;

enum ActivityAction: string
{
    case LoggedIn = 'logged_in';
    case LoggedOut = 'logged_out';
    case UserCreated = 'user_created';
    case UserUpdated = 'user_updated';
    case UserDeleted = 'user_deleted';
    case ProfileUpdated = 'profile_updated';
    case AnnouncementCreated = 'announcement_created';
    case AnnouncementUpdated = 'announcement_updated';
    case AnnouncementDeleted = 'announcement_deleted';
    case FolderCreated = 'folder_created';
    case FolderDeleted = 'folder_deleted';
    case DocumentUploaded = 'document_uploaded';
    case DocumentDeleted = 'document_deleted';
    case VehicleCreated = 'vehicle_created';
    case VehicleUpdated = 'vehicle_updated';
    case VehicleDeleted = 'vehicle_deleted';
    case EmployeeCreated = 'employee_created';
    case EmployeeUpdated = 'employee_updated';
    case EmployeeDeleted = 'employee_deleted';
    case DriverCreated = 'driver_created';
    case DriverUpdated = 'driver_updated';
    case DriverDeleted = 'driver_deleted';
    case ItemCreated = 'item_created';
    case ItemUpdated = 'item_updated';
    case ItemDeleted = 'item_deleted';
    case StockCreated = 'stock_created';
    case StockUpdated = 'stock_updated';
    case StockDeleted = 'stock_deleted';
    case PurchaseCreated = 'purchase_created';
    case PurchaseUpdated = 'purchase_updated';
    case PurchaseDeleted = 'purchase_deleted';
    case PurchaseApproved = 'purchase_approved';
    case PurchaseRejected = 'purchase_rejected';
    case SupplierCreated = 'supplier_created';
    case SupplierUpdated = 'supplier_updated';
    case SupplierDeleted = 'supplier_deleted';
    case ItemRequestCreated = 'item_request_created';
    case ItemRequestUpdated = 'item_request_updated';
    case ItemRequestDeleted = 'item_request_deleted';
    case ItemRequestApproved = 'item_request_approved';
    case ItemRequestRejected = 'item_request_rejected';
    case GarageMaintenanceCreated = 'garage_maintenance_created';
    case GarageMaintenanceUpdated = 'garage_maintenance_updated';
    case GarageMaintenanceDeleted = 'garage_maintenance_deleted';
    case OnRoadMaintenanceCreated = 'on_road_maintenance_created';
    case OnRoadMaintenanceUpdated = 'on_road_maintenance_updated';
    case OnRoadMaintenanceDeleted = 'on_road_maintenance_deleted';
    case Unknown = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::LoggedIn => 'Signed in',
            self::LoggedOut => 'Signed out',
            self::UserCreated => 'User created',
            self::UserUpdated => 'User updated',
            self::UserDeleted => 'User deleted',
            self::ProfileUpdated => 'Profile updated',
            self::AnnouncementCreated => 'Announcement published',
            self::AnnouncementUpdated => 'Announcement updated',
            self::AnnouncementDeleted => 'Announcement removed',
            self::FolderCreated => 'Folder created',
            self::FolderDeleted => 'Folder deleted',
            self::DocumentUploaded => 'Document uploaded',
            self::DocumentDeleted => 'Document deleted',
            self::VehicleCreated => 'Vehicle created',
            self::VehicleUpdated => 'Vehicle updated',
            self::VehicleDeleted => 'Vehicle deleted',
            self::EmployeeCreated => 'Employee created',
            self::EmployeeUpdated => 'Employee updated',
            self::EmployeeDeleted => 'Employee deleted',
            self::DriverCreated => 'Driver created',
            self::DriverUpdated => 'Driver updated',
            self::DriverDeleted => 'Driver deleted',
            self::ItemCreated => 'Item created',
            self::ItemUpdated => 'Item updated',
            self::ItemDeleted => 'Item deleted',
            self::StockCreated => 'Stock created',
            self::StockUpdated => 'Stock updated',
            self::StockDeleted => 'Stock deleted',
            self::PurchaseCreated => 'Purchase created',
            self::PurchaseUpdated => 'Purchase updated',
            self::PurchaseDeleted => 'Purchase deleted',
            self::PurchaseApproved => 'Purchase approved',
            self::PurchaseRejected => 'Purchase rejected',
            self::SupplierCreated => 'Supplier created',
            self::SupplierUpdated => 'Supplier updated',
            self::SupplierDeleted => 'Supplier deleted',
            self::ItemRequestCreated => 'Item request created',
            self::ItemRequestUpdated => 'Item request updated',
            self::ItemRequestDeleted => 'Item request deleted',
            self::ItemRequestApproved => 'Item request approved',
            self::ItemRequestRejected => 'Item request rejected',
            self::GarageMaintenanceCreated => 'Garage maintenance created',
            self::GarageMaintenanceUpdated => 'Garage maintenance updated',
            self::GarageMaintenanceDeleted => 'Garage maintenance deleted',
            self::OnRoadMaintenanceCreated => 'On-road maintenance created',
            self::OnRoadMaintenanceUpdated => 'On-road maintenance updated',
            self::OnRoadMaintenanceDeleted => 'On-road maintenance deleted',
            self::Unknown => 'Activity recorded',
        };
    }
}
